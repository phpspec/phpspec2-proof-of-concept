<?php

namespace PHPSpec2\Exception\Example;

class ObjectsNotEqualException extends FailureException
{
    private $expectedObject;
    private $actualObject;

    public function __construct($message, $expectedObject, $actualObject)
    {
        parent::__construct($message);

        $this->expectedObject = $expectedObject;
        $this->actualObject = $actualObject;
    }
}
