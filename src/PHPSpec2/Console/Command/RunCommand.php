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
use PHPSpec2\Diff;

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
            new InputOption('example', 'e', InputOption::VALUE_REQUIRED, 'Run examples matching pattern'),
            new InputOption('fail-fast', null, InputOption::VALUE_NONE, 'Abort the run on first failure')
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

        $presenter = new TaggedPresenter();

        $matchers = new Matcher\MatchersCollection();
        $matchers->add(new Matcher\IdentityMatcher($presenter));
        $matchers->add(new Matcher\ComparisonMatcher($presenter));
        $matchers->add(new Matcher\ThrowMatcher($presenter));
        $matchers->add(new Matcher\CountMatcher($presenter));
        $matchers->add(new Matcher\TypeMatcher($presenter));
        $matchers->add(new Matcher\ObjectStateMatcher($presenter));

        $mocker    = new MockeryMocker;
        $unwrapper = new ArgumentsUnwrapper;

        // setup specs locator and runner
        $locator = new Locator(new SpecificationsClassLoader);
        $runner  = new Runner(new EventDispatcher(), $matchers, $mocker, $unwrapper, $input->getOptions());

        // setup differ
        $differ = new Diff\Diff;
        $differ->addEngine(new Diff\StringEngine);

        // setup formatter
        $formatter = new Formatter\PrettyFormatter($presenter, $differ);
        $formatter->setIO($io);
        $runner->getEventDispatcher()->addSubscriber($formatter);

        // setup listeners
        $runner->getEventDispatcher()->addSubscriber(new ClassNotFoundListener($io));
        $runner->getEventDispatcher()->addSubscriber(new MethodNotFoundListener($io));

        // setup statistics collector
        $collector = new StatisticsCollector;
        $runner->getEventDispatcher()->addSubscriber($collector);

        $specifications = $locator->getSpecifications($input->getArgument('spec'));

        $runner->getEventDispatcher()->dispatch('beforeSuite', new SuiteEvent($collector));
        foreach ($specifications as $spec) {
            $runner->runSpecification($spec);
        }
        $runner->getEventDispatcher()->dispatch('afterSuite', new SuiteEvent($collector));

        return intval(ExampleEvent::PASSED !== $collector->getGlobalResult());
    }
}
