<?php

namespace PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
// use Symfony\Component\Console\Input\InputOption;
// use Symfony\Component\EventDispatcher\EventDispatcher;

class DescribeCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('describe');

        $this->setDefinition(array(
            new InputArgument('desc', InputArgument::OPTIONAL, ''),
            new InputArgument('spec', InputArgument::OPTIONAL, 'Specs to describe')
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}
