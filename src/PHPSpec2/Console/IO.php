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
        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('yellow'));

        $output->getFormatter()->setStyle('lineno', new OutputFormatterStyle('white', 'black'));
        $output->getFormatter()->setStyle('code', new OutputFormatterStyle('white'));
        $output->getFormatter()->setStyle('hl', new OutputFormatterStyle('black', 'yellow', array('bold')));

        $output->getFormatter()->setStyle('trace-class', new OutputFormatterStyle('red'));
        $output->getFormatter()->setStyle('trace-func', new OutputFormatterStyle('blue'));
        $output->getFormatter()->setStyle('trace-type', new OutputFormatterStyle('white'));
        $output->getFormatter()->setStyle('trace-args', new OutputFormatterStyle('white'));

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
