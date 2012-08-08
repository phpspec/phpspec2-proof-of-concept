<?php

namespace PHPSpec2\Exception\Example;

class ArraysNotEqualException extends FailureException
{
    private $expectedArray;
    private $actualArray;

    public function __construct($message, $expectedArray, $actualArray)
    {
        parent::__construct($message);

        $this->expectedArray = $expectedArray;
        $this->actualArray = $actualArray;
    }
}
