<?php

namespace PHPSpec2\Exception\Example;

class StringsNotEqualException extends FailureException
{
    private $expectedString;
    private $actualString;

    public function __construct($message, $expectedString, $actualString)
    {
        parent::__construct($message);
        $this->expectedString = $expectedString;
        $this->actualString = $actualString;
    }

}
