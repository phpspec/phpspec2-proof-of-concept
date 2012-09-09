<?php

namespace PHPSpec2\Exception\Stub;

class ClassDoesNotExistsException extends StubException
{
    public function __construct($class)
    {
        parent::__construct(sprintf(
            'Class <value>%s</value> does not exists.', $class
        ));
    }
}
