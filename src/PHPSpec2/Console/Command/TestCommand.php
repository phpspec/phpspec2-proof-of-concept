<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\EventDispatcher\EventDispatcher;

use PHPSpec2\Locator;
use PHPSpec2\Tester;
use PHPSpec2\Matcher;

class TestCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('test');

        $this->setDefinition(array(
            new InputArgument('specs', InputArgument::OPTIONAL, 'Specs to run')
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locator = new Locator($input->getArgument('specs'));
        $tester  = new Tester(new EventDispatcher(), array(
            new Matcher\ShouldReturnMatcher,
            new Matcher\ShouldContainMatcher,
        ));

        foreach ($locator->getSpecifications() as $spec) {
            $tester->testSpecification($spec);
        }
    }
}
