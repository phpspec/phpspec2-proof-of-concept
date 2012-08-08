<?php

namespace PHPSpec2\Exception\Example;

class IntegersNotEqualException extends FailureException
{
    private $expectedInteger;
    private $actualInteger;

    public function __construct($message, $expectedInteger, $actualInteger)
    {
        parent::__construct($message);
        $this->expectedInteger = $expectedInteger;
        $this->actualInteger = $actualInteger;
    }

}
