<?php

namespace PHPSpec2\Exception\Stub;

class MethodNotFoundException extends StubException
{
    public function __construct($object, $method)
    {
        $this->object = $object;
        $this->method = $method;

        parent::__construct(
            sprintf('Method "%s" not found in class "%s"', $method, get_class($object))
        );
    }
}
