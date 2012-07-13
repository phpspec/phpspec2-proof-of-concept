<?php

namespace PHPSpec2\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class IO
{
    private $input;
    private $output;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $output->getFormatter()->setStyle('pending', new OutputFormatterStyle('yellow'));
        $output->getFormatter()->setStyle('failed', new OutputFormatterStyle('red'));
        $output->getFormatter()->setStyle('passed', new OutputFormatterStyle('green'));

        $this->input  = $input;
        $this->output = $output;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
