<?php

namespace PHPSpec2\Exception\Example;

class NotEqualException extends FailureException
{
    private $expected;
    private $actual;

    public function __construct($message, $expected, $actual)
    {
        parent::__construct(
            ucfirst(sprintf($message, gettype($expected), gettype($actual)))
        );

        $this->expected = $expected;
        $this->actual   = $actual;
    }

    public function getExpected()
    {
        return $this->expected;
    }

    public function getActual()
    {
        return $this->actual;
    }
}
