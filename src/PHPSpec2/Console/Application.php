<?php

namespace PHPSpec2\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;

class Application extends BaseApplication
{
    /**
     * {@inheritdoc}
     */
    public function __construct($version)
    {
        parent::__construct('PHPSpec2', $version);

        $this->add(new Command\TestCommand);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new InputDefinition(array(
            new InputOption('--help',    '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--verbose', '-v', InputOption::VALUE_NONE, 'Increase verbosity of exceptions.'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this behat version.'),
            new InputOption('--config',  '-c', InputOption::VALUE_REQUIRED, 'Specify config file to use.'),
            new InputOption('--profile', '-p', InputOption::VALUE_REQUIRED, 'Specify config profile to use.')
        ));
    }

    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $input The input interface
     *
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'test';
    }
}
