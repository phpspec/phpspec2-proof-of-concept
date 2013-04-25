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

use InvalidArgumentException;

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
            new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'Formatter', 'progress'),
            new InputOption('bootstrap', null, InputOption::VALUE_REQUIRED, 'Run a bootstrap file before start', false)
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($bootstrap = $input->getOption("bootstrap") ) {
            if (null === $bootstrap) {
                throw new InvalidArgumentException('The --bootstrap option needs a value');
            }

            if (!is_file($bootstrap)) {
                throw new InvalidArgumentException("The bootstrap file ({$bootstrap}) doesn't exist");
            } else if (pathinfo($bootstrap, PATHINFO_EXTENSION) !== "php") {
                throw new InvalidArgumentException("The bootstrap file ({$bootstrap}) isn't a valid php file");
            }

            require $bootstrap;
        }

        $output->setFormatter(new Console\Formatter($output->isDecorated()));
        $c = $this->getApplication()->getContainer();

        $c->set('console.input', $input);
        $c->set('console.output', $output);
        $c->set('console.helpers', $this->getHelperSet());

        $specs = $c('locator')->getSpecifications($input->getArgument('spec'));
        $c('event_dispatcher')->dispatch('beforeSuite', new Event\SuiteEvent);

        $result = 0;
        $startTime = microtime(true);
        foreach ($specs as $spec) {
            $result = max($result, $c('runner')->runSpecification($spec));
        }

        $c('event_dispatcher')->dispatch('afterSuite', new Event\SuiteEvent(
            microtime(true) - $startTime, $result
        ));

        return intval(Event\ExampleEvent::PASSED !== $result);
    }
}
