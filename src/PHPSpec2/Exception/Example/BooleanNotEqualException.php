<?php

namespace PHPSpec2\Exception\Example;

class BooleanNotEqualException extends FailureException
{
    private $expectedBoolean;
    private $actual;

    public function __construct($message, $expectedBoolean, $actual)
    {
        parent::__construct($message);

        $this->expectedBoolean = $expectedBoolean;
        $this->actual = $actual;
    }
}
