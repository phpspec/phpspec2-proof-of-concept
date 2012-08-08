<?php

namespace PHPSpec2\Exception\Example;

class ResourcesNotEqualException extends FailureException
{
    private $expectedResource;
    private $actualResource;

    public function __construct($message, $expectedResource, $actualResource)
    {
        parent::__construct($message);
        $this->expectedResource = $expectedResource;
        $this->actualResource = $actualResource;
    }

}
