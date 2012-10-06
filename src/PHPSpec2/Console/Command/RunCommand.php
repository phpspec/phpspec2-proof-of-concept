<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;

use PHPSpec2\Console\IO;
use PHPSpec2\Runner\Locator;
use PHPSpec2\Runner\Runner;
use PHPSpec2\Matcher;
use PHPSpec2\Listener\StatisticsCollector;
use PHPSpec2\Formatter;
use PHPSpec2\Console\Formatter as CliOutputFormatter;
use PHPSpec2\Event\SuiteEvent;
use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Formatter\Presenter\TaggedPresenter;
use PHPSpec2\Listener\ClassNotFoundListener;
use PHPSpec2\Listener\MethodNotFoundListener;
use PHPSpec2\Mocker\MockeryMocker;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Loader\SpecificationsClassLoader;
use PHPSpec2\Formatter\Presenter\Differ;

class RunCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('run');

        $this->setDefinition(array(
            new InputArgument('spec', InputArgument::OPTIONAL, 'Specs to run', 'spec'),
            new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'Formatter', 'pretty'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // setup IO
        $io = new IO($input, $output, $this->getHelperSet());
        $output->setFormatter(new CliOutputFormatter($io->isDecorated()));

        // setup differ
        $differ = new Differ\Differ;
        $differ->addEngine(new Differ\StringEngine);

        // setup presenter
        $presenter = new TaggedPresenter($differ);

        $mocker    = new MockeryMocker;
        $unwrapper = new ArgumentsUnwrapper;

        $matchers = new Matcher\MatchersCollection();
        $matchers->add(new Matcher\IdentityMatcher($presenter));
        $matchers->add(new Matcher\ComparisonMatcher($presenter));
        $matchers->add(new Matcher\ThrowMatcher($unwrapper, $presenter));
        $matchers->add(new Matcher\CountMatcher($presenter));
        $matchers->add(new Matcher\TypeMatcher($presenter));
        $matchers->add(new Matcher\ObjectStateMatcher($presenter));

        // setup specs locator and runner
        $locator = new Locator(new SpecificationsClassLoader);
        $runner  = new Runner(new EventDispatcher(), $matchers, $mocker, $unwrapper, $input->getOptions());

        // setup statistics collector
        $collector = new StatisticsCollector;
        $runner->getEventDispatcher()->addSubscriber($collector);

        // setup formatter
        if ('progress' === $input->getOption('format')) {
            $formatter = new Formatter\ProgressFormatter;
        } else {
            $formatter = new Formatter\PrettyFormatter;
        }

        $formatter->setIO($io);
        $formatter->setPresenter($presenter);
        $formatter->setStatisticsCollector($collector);
        $runner->getEventDispatcher()->addSubscriber($formatter);

        // setup listeners
        $runner->getEventDispatcher()->addSubscriber(new ClassNotFoundListener($io));
        $runner->getEventDispatcher()->addSubscriber(new MethodNotFoundListener($io));

        $specifications = $locator->getSpecifications($input->getArgument('spec'));

        $runner->getEventDispatcher()->dispatch('beforeSuite', new SuiteEvent($collector));
        $startTime = microtime(true);
        $result = 0;
        foreach ($specifications as $spec) {
            $result = max($result, $runner->runSpecification($spec));
        }
        $runner->getEventDispatcher()->dispatch('afterSuite', new SuiteEvent(
            microtime(true) - $startTime, $result
        ));

        return intval(ExampleEvent::PASSED !== $result);
    }
}
