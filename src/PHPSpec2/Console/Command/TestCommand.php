<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;

use PHPSpec2\Console\IO;
use PHPSpec2\Locator;
use PHPSpec2\Tester;
use PHPSpec2\Matcher;
use PHPSpec2\StatisticsCollector;
use PHPSpec2\Formatter;
use PHPSpec2\Event\SuiteEvent;
use PHPSpec2\Event\ExampleEvent;

class TestCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('test');

        $this->setDefinition(array(
            new InputArgument('spec', InputArgument::OPTIONAL, 'Specs to run')
        ));
        
        $this->addOption('example', 'e', InputOption::VALUE_REQUIRED, 'Run examples matching a given pattern');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // setup IO
        $io = new IO($input, $output);

        $matchers = new Matcher\MatchersCollection();
        $matchers->add(new Matcher\IdentityMatcher);
        $matchers->add(new Matcher\ComparisonMatcher);
        $matchers->add(new Matcher\CountMatcher);
        $matchers->add(new Matcher\TrueMatcher);
        $matchers->add(new Matcher\ThrowMatcher);
        $matchers->add(new Matcher\TypeMatcher);

        // setup specs locator and tester
        $locator = new Locator($input->getArgument('spec'));
        $tester  = new Tester(new EventDispatcher(), $matchers, $input->getOption('example'));

        // setup formatter
        $formatter = new Formatter\PrettyFormatter;
        $formatter->setIO($io);
        $tester->getEventDispatcher()->addSubscriber($formatter);

        // setup statistics collector
        $collector = new StatisticsCollector;
        $tester->getEventDispatcher()->addSubscriber($collector);
        $tester->getEventDispatcher()->dispatch('beforeSuite', new SuiteEvent($collector));

        foreach ($locator->getSpecifications() as $spec) {
            $tester->testSpecification($spec);
        }

        $tester->getEventDispatcher()->dispatch('afterSuite', new SuiteEvent($collector));

        return intval(ExampleEvent::PASSED !== $collector->getGlobalResult());
    }
}
