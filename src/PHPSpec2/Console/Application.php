<?php

namespace PHPSpec2\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

use ArrayAccess;
use Closure;
use InvalidArgumentException;

class Application extends BaseApplication implements ArrayAccess
{
    private $items;

    /**
     * {@inheritdoc}
     */
    public function __construct($version)
    {
        parent::__construct('PHPSpec2', $version);

        $this['parameters.format'] = 'progress';

        $this['console.commands'] = array();
        $this['io'] = $this->share(function($c) {
            return new Console\IO(
                $c['console.input'],
                $c['console.output'],
                $c['console.helpers']
            );
        });

        $this['event_dispatcher.listeners'] = array();
        $this['event_dispatcher'] = $this->share(function($c) {
            $dispatcher = new EventDispatcher;

            foreach ($c['event_dispatcher.listeners'] as $listener) {
                $dispatcher->addSubscriber($listener);
            }

            return $dispatcher;
        });

        $this['differ.engines'] = array();
        $this['differ'] = function($c) {
            $differ = new Presenter\Differ\Differ;

            foreach ($c['differ.engines'] as $engine) {
                $differ->addEngine($engine);
            }

            return $differ;
        };

        $this->extend('differ.engines',
            $this['differ.engines.string'] = $this->share(function($c) {
                return new Presenter\Differ\StringEngine;
            })
        );

        $this['value_presenter'] = $this->share(function($c) {
            return new Presenter\TaggedPresenter($c['differ']);
        });

        $this['mocker'] = $this->share(function($c) {
            return new Mocker\MockeryMocker;
        });

        $this['arguments_unwrapper'] = $this->share(function($c) {
            return new ArgumentsUnwrapper;
        });

        $this->extend('event_dispatcher.listeners',
            $this['statistics_collector'] = $this->share(function($c) {
                return new Listener\StatisticsCollector;
            })
        );

        $this->extend('event_dispatcher.listeners',
            $this['formatter'] = $this->share(function($c) {
                if ('progress' === $c['parameters.format']) {
                    $formatter = new Formatter\ProgressFormatter;
                } else {
                    $formatter = new Formatter\PrettyFormatter;
                }

                $formatter->setIO($c['io']);
                $formatter->setPresenter($c['value_presenter']);
                $formatter->setStatisticsCollector($c['statistics_collector']);

                return $formatter;
            })
        );

        $this['specifications_loader'] = $this->share(function($c) {
            return new Loader\SpecificationsClassLoader;
        });

        $this['locator'] = $this->share(function($c) {
            return new Runner\Locator($c['specifications_loader']);
        });

        $this['runner.subject_guessers'] = array();
        $this['runner.specification_initializers'] = array();
        $this['runner.example_initializers'] = array();
        $this['runner'] = $this->share(function($c) {
            $runner = new Runner\Runner(
                $c['event_dispatcher'],
                $c['mocker']
            );

            foreach ($c['runner.subject_guessers'] as $guesser) {
                $runner->registerSubjectGuesser($guesser);
            }
            foreach ($c['runner.specification_initializers'] as $initializer) {
                $runner->registerSpecificationInitializer($initializer);
            }
            foreach ($c['runner.example_initializers'] as $initializer) {
                $runner->registerExampleInitializer($initializer);
            }

            return $runner;
        });

        $this->extend('runner.subject_guessers', function($c) {
            return new DefaultSubjectGuesser($c['arguments_unwrapper']);
        });

        $this->extend('runner.specification_initializers', function($c) {
            return new Initializer\DefaultMatchersInitializer(
                $c['value_presenter'],
                $c['arguments_unwrapper']
            );
        });

        $this->extend('runner.example_initializers', function($c) {
            return new Initializer\ArgumentsProphetsInitializer(
                $c['initializer.function_parameters_reader'],
                $c['mocker'],
                $c['arguments_unwrapper']
            );
        });

        $this['initializer.function_parameters_reader'] = function($c) {
            return new Initializer\FunctionParametersReader;
        };

        $this->extend('event_dispatcher.listeners', function($c) {
            return new Listener\ClassNotFoundListener($c['io']);
        });

        $this->extend('event_dispatcher.listeners', function($c) {
            return new Listener\MethodNotFoundListener($c['io']);
        });

        $this->extend('console.commands', function($c) {
            return new Command\RunCommand;
        });

        $this->extend('console.commands', function($c) {
            return new Command\DescribeCommand;
        });

        foreach ($this['console.commands'] as $command) {
            $this->add($command);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if (!($name = $this->getCommandName($input))) {
            $input = new ArrayInput(array('command' => 'run'));
        }

        parent::doRun($input, $output);
    }

    public function offsetSet($id, $value)
    {
        $this->items[$id] = $value;
    }

    public function offsetGet($id)
    {
        if (!$this->offsetExists($id)) {
            throw new InvalidArgumentException(sprintf(
                "Service/Parameter `%s` not found.", $id
            ));
        }

        $value = $this->items[$id];

        if (is_array($value)) {
            return array_map(array($this, 'unwrapItem'), $value);
        }

        return $this->unwrapItem($value);
    }

    public function extend($id, $value)
    {
        if (!$this->offsetExists($id)) {
            $this->items[$id] = array();
        }

        $this->items[$id][] = $value;
    }

    public function unwrapItem($value)
    {
        return $value instanceof Closure ? $value($this) : $value;
    }

    public function offsetExists($id)
    {
        return isset($this->items[$id]);
    }

    public function offsetUnset($id)
    {
        if (!$this->offsetExists($id)) {
            throw new InvalidArgumentException(sprintf(
                "Service/Parameter `%s` not found.", $id
            ));
        }

        unset($this->items[$id]);
    }

    public function share(Closure $factory)
    {
        return function($c) use($factory) {
            static $object;
            return $object ?: $object = $factory($c);
        };
    }
}
