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
use PHPSpec2\Formatter;

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
            new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'Formatter {pretty|progress}', 'progress'),
            new InputOption('bootstrap', 'b', InputOption::VALUE_OPTIONAL, 'Path to bootstrap file'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$output->setBootstrapper(new Console\Formatter($output->isDecorated()));
        $output->setFormatter(new Console\Formatter($output->isDecorated()));
        $c = $this->getApplication()->getContainer();

        $c->set('format', $input->getOption('format'));
        $c->set('console.input', $input);
        $c->set('console.output', $output);
        $c->set('console.helpers', $this->getHelperSet());

        $specs = $c('locator')->getSpecifications($input->getArgument('spec'));
        $c('event_dispatcher')->dispatch('beforeSuite', new Event\SuiteEvent);

        $result = 0;
        $startTime = microtime(true);
        $c('runner')->runBootstrap($input->getOption('bootstrap'));
        foreach ($specs as $spec) {
            $result = max($result, $c('runner')->runSpecification($spec));
        }

        $c('event_dispatcher')->dispatch('afterSuite', new Event\SuiteEvent(
            microtime(true) - $startTime, $result
        ));

        return intval(Event\ExampleEvent::PASSED !== $result);
    }
}
