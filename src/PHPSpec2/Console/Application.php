<?php

namespace PHPSpec2\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

use PHPSpec2\ServiceContainer;
use PHPSpec2\Console;
use PHPSpec2\Loader;
use PHPSpec2\Runner;
use PHPSpec2\Listener;
use PHPSpec2\Mocker;
use PHPSpec2\Formatter;
use PHPSpec2\Formatter\Presenter;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Prophet\DefaultSubjectGuesser;
use PHPSpec2\Initializer;
use PHPSpec2\Extension\Configuration;
use PHPSpec2\Extension\ExtensionInterface;

use InvalidArgumentException;

class Application extends BaseApplication
{
    private $container;

    /**
     * {@inheritdoc}
     */
    public function __construct($version)
    {
        parent::__construct('PHPSpec2', $version);

        $this->container = $c = new ServiceContainer;

        $c->set('format', 'progress');

        $c->set('console.commands', array());
        $c->set('io', $c->share(function($c) {
            return new Console\IO(
                $c('console.input'),
                $c('console.output'),
                $c('console.helpers')
            );
        }));

        $c->set('event_dispatcher.listeners', array());
        $c->set('event_dispatcher', $c->share(function($c) {
            $dispatcher = new EventDispatcher;

            foreach ($c('event_dispatcher.listeners') as $listener) {
                $dispatcher->addSubscriber($listener);
            }

            return $dispatcher;
        }));

        $c->set('differ.engines', array());
        $c->set('differ', function($c) {
            $differ = new Presenter\Differ\Differ;

            foreach ($c('differ.engines') as $engine) {
                $differ->addEngine($engine);
            }

            return $differ;
        });

        $c->extend('differ.engines',
            $c->set('differ.engines.string', $c->share(function($c) {
                return new Presenter\Differ\StringEngine;
            }))
        );

        $c->extend('differ.engines',
            $c->set('differ.engines.array', $c->share(function($c) {
                return new Presenter\Differ\ArrayEngine();
            }))
        );

        $c->set('value_presenter', $c->share(function($c) {
            return new Presenter\TaggedPresenter($c('differ'));
        }));

        $c->set('mocker', $c->share(function($c) {
            return new Mocker\MockeryMocker($c('value_presenter'));
        }));

        $c->set('arguments_unwrapper', $c->share(function($c) {
            return new ArgumentsUnwrapper;
        }));

        $c->extend('event_dispatcher.listeners',
            $c->set('statistics_collector', $c->share(function($c) {
                return new Listener\StatisticsCollector;
            }))
        );

        $c->extend('event_dispatcher.listeners',
            $c->set('formatter', $c->share(function($c) {
                if ('progress' === $c('format')) {
                    $formatter = new Formatter\ProgressFormatter;
                } else {
                    $formatter = new Formatter\PrettyFormatter;
                }

                $formatter->setIO($c('io'));
                $formatter->setPresenter($c('value_presenter'));
                $formatter->setStatisticsCollector($c('statistics_collector'));

                return $formatter;
            }))
        );

        $c->set('specifications_loader', $c->share(function($c) {
            return new Loader\SpecificationsClassLoader;
        }));

        $c->set('locator', $c->share(function($c) {
            return new Runner\Locator($c('specifications_loader'));
        }));

        $c->set('runner.subject_guessers', array());
        $c->set('runner.specification_initializers', array());
        $c->set('runner.example_initializers', array());

        $c->set('runner', $c->share(function($c) {
            $runner = new Runner\Runner(
                $c('event_dispatcher'),
                $c('mocker'),
                $c('value_presenter')
            );

            foreach ($c('runner.subject_guessers') as $guesser) {
                $runner->registerSubjectGuesser($guesser);
            }
            foreach ($c('runner.specification_initializers') as $initializer) {
                $runner->registerSpecificationInitializer($initializer);
            }
            foreach ($c('runner.example_initializers') as $initializer) {
                $runner->registerExampleInitializer($initializer);
            }

            return $runner;
        }));

        $c->extend('runner.subject_guessers', function($c) {
            return new DefaultSubjectGuesser($c('arguments_unwrapper'), $c('value_presenter'));
        });

        $c->extend('runner.specification_initializers', function($c) {
            return new Initializer\DefaultMatchersInitializer(
                $c('value_presenter'),
                $c('arguments_unwrapper')
            );
        });

        $c->extend('runner.specification_initializers', function($c) {
            return new Initializer\CustomMatchersInitializer(
                $c('value_presenter'),
                $c('arguments_unwrapper')
            );
        });

        $c->extend('runner.example_initializers', function($c) {
            return new Initializer\ArgumentsProphetsInitializer(
                $c('initializer.function_parameters_reader'),
                $c('mocker'),
                $c('arguments_unwrapper'),
                $c('value_presenter')
            );
        });

        $c->set('initializer.function_parameters_reader', function($c) {
            return new Initializer\FunctionParametersReader;
        });

        $c->extend('event_dispatcher.listeners', function($c) {
            return new Listener\ClassNotFoundListener($c('io'));
        });

        $c->extend('event_dispatcher.listeners', function($c) {
            return new Listener\MethodNotFoundListener($c('io'));
        });

        $c->extend('console.commands', function($c) {
            return new Command\RunCommand;
        });

        $c->extend('console.commands', function($c) {
            return new Command\DescribeCommand;
        });
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $configuration = new Configuration($this->container);
        if (is_file('phpspec.yml')) {
            $configuration->read('phpspec.yml');
        }
        if ($this->container->has('extensions')) {
            foreach ($this->container->get('extensions') as $class) {
                $extension = new $class;
                if (!$extension instanceof ExtensionInterface) {
                    throw new InvalidArgumentException(sprintf(
                        'phpspec2 extensions should implement ExtensionInterface. "%s" does not.',
                        $class
                    ));
                }

                $extension->initialize($this->container);
            }
        }

        foreach ($this->container->get('console.commands') as $command) {
            $this->add($command);
        }

        if (!($name = $this->getCommandName($input))
         && !$input->hasParameterOption('-h')
         && !$input->hasParameterOption('--help')) {
            $input = new ArrayInput(array('command' => 'run'));
        }

        return parent::doRun($input, $output);
    }
}
