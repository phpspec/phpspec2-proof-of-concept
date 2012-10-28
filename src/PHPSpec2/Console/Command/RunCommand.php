<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;

use PHPSpec2\Event;
use PHPSpec2\Console;
use PHPSpec2\Loader;
use PHPSpec2\Runner;
use PHPSpec2\Matcher;
use PHPSpec2\Listener;
use PHPSpec2\Mocker;
use PHPSpec2\Formatter;
use PHPSpec2\Formatter\Presenter;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Prophet\DefaultSubjectGuesser;
use PHPSpec2\Initializer;

class RunCommand extends Command
{
    private $dispatcher;

    /**
     * Initializes command.
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        parent::__construct('run');

        $this->dispatcher = $dispatcher;

        $this->setDefinition(array(
            new InputArgument('spec', InputArgument::OPTIONAL, 'Specs to run', 'spec'),
            new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'Formatter', 'progress'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setFormatter(new Console\Formatter($output->isDecorated()));

        $this->io  = new Console\IO($input, $output, $this->getHelperSet());
        $presenter = $this->createPresenter();
        $mocker    = $this->createMocker();
        $unwrapper = $this->createArgumentsUnwrapper();
        $collector = $this->createStatisticsCollector();
        $formatter = $this->createFormatter($input->getOption('format'), $presenter, $collector);

        $specs  = $this->createLocator()->getSpecifications($input->getArgument('spec'));
        $runner = $this->createRunner($mocker, $unwrapper);

        // TODO: extension points
        $runner->registerSubjectGuesser(new DefaultSubjectGuesser);
        $runner->registerSpecificationInitializer(new Initializer\DefaultMatchersInitializer(
            $presenter, $unwrapper
        ));
        $runner->registerExampleInitializer(new Initializer\ArgumentsProphetsInitializer(
            new Initializer\FunctionParametersReader, $mocker, $unwrapper
        ));

        $this->configureAdditionalListeners();
        $this->dispatcher->dispatch('beforeSuite', new Event\SuiteEvent($collector));

        $result = 0;
        $startTime = microtime(true);
        foreach ($specs as $spec) {
            $result = max($result, $runner->runSpecification($spec));
        }

        $this->dispatcher->dispatch('afterSuite', new Event\SuiteEvent(
            microtime(true) - $startTime, $result
        ));

        return intval(Event\ExampleEvent::PASSED !== $result);
    }

    protected function createPresenter()
    {
        $differ = new Presenter\Differ\Differ;
        $differ->addEngine(new Presenter\Differ\StringEngine);

        return new Presenter\TaggedPresenter($differ);
    }

    protected function createMocker()
    {
        return new Mocker\MockeryMocker;
    }

    protected function createArgumentsUnwrapper()
    {
        return new ArgumentsUnwrapper;
    }

    protected function createLocator()
    {
        return new Runner\Locator(new Loader\SpecificationsClassLoader);
    }

    protected function createRunner(Mocker\MockerInterface $mocker, ArgumentsUnwrapper $unwrapper)
    {
        return new Runner\Runner($this->dispatcher, $mocker, $unwrapper);
    }

    protected function createStatisticsCollector()
    {
        $collector = new Listener\StatisticsCollector;
        $this->dispatcher->addSubscriber($collector);

        return $collector;
    }

    protected function createFormatter($format, Presenter\PresenterInterface $presenter,
                                       Listener\StatisticsCollector $collector)
    {
        if ('progress' === $format) {
            $formatter = new Formatter\ProgressFormatter;
        } else {
            $formatter = new Formatter\PrettyFormatter;
        }

        $formatter->setIO($this->io);
        $formatter->setPresenter($presenter);
        $formatter->setStatisticsCollector($collector);

        $this->dispatcher->addSubscriber($formatter);

        return $formatter;
    }

    protected function configureAdditionalListeners()
    {
        $this->dispatcher->addSubscriber(new Listener\ClassNotFoundListener($this->io));
        $this->dispatcher->addSubscriber(new Listener\MethodNotFoundListener($this->io));
    }
}
