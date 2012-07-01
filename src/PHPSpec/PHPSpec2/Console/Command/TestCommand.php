<?php

namespace PHPSpec\PHPSpec2\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class TestCommand extends Command
{
    /**
     * Initializes command.
     */
    public function __construct()
    {
        parent::__construct('test');

        $this->setDefinition(array());
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
