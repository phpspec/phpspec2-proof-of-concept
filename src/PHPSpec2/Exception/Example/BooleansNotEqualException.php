<?php

namespace PHPSpec2\Exception\Example;

class BooleansNotEqualException extends FailureException
{
    private $expectedBoolean;
    private $actualBoolean;

    public function __construct($message, $expectedBoolean, $actualBoolean)
    {
        parent::__construct($message);
        $this->expectedBoolean = $expectedBoolean;
        $this->actualBoolean = $actualBoolean;
    }

}
