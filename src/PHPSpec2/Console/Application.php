<?php

namespace PHPSpec2\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputArgument;
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

        $this->add(new Command\RunCommand);
        $this->add(new Command\DescribeCommand);
    }
}
