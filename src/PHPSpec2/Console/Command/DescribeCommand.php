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
            new InputArgument('', InputArgument::REQUIRED, ''),
            new InputArgument('spec', InputArgument::REQUIRED, 'Specs to describe')
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $fullyQualifiedClassName = $input->getArgument('spec');
        // 
        // $file = Generator\File::fromClass($fullyQualifiedClassName);
        // 
        // if ($file->exists()) {
        //     throw new CommandException("Create create spec for $fullyQualifiedClassName. Spec already exists.");
        // }
        // $file->create();
        // 
        // $code = Generator\Code::emptySpec($fullyQualifiedClassName);
        // $file->write($code);
    }
}
