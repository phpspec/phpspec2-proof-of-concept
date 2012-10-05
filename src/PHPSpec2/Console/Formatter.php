<?php

namespace PHPSpec2\Console;

use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Formatter extends OutputFormatter
{
    public function __construct($decorated = null, array $styles = array())
    {
        parent::__construct($decorated, $styles);

        $this->setStyle('pending', new OutputFormatterStyle('yellow'));
        $this->setStyle('failed', new OutputFormatterStyle('red'));
        $this->setStyle('passed', new OutputFormatterStyle('green'));
        $this->setStyle('value', new OutputFormatterStyle('yellow'));

        $this->setStyle('lineno', new OutputFormatterStyle(null, 'black'));
        $this->setStyle('code', new OutputFormatterStyle('white'));
        $this->setStyle('hl', new OutputFormatterStyle('black', 'yellow', array('bold')));

        $this->setStyle('trace', new OutputFormatterStyle());
        $this->setStyle('trace-class', new OutputFormatterStyle('blue'));
        $this->setStyle('trace-func', new OutputFormatterStyle('blue'));
        $this->setStyle('trace-type', new OutputFormatterStyle());
        $this->setStyle('trace-args', new OutputFormatterStyle());

        $this->setStyle('diff-add', new OutputFormatterStyle('green'));
        $this->setStyle('diff-del', new OutputFormatterStyle('red'));
    }
}
