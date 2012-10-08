<?php

namespace PHPSpec2\Exception\Example;

class MockerException extends FailureException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
