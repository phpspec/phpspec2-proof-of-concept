<?php

namespace PHPSpec2\Stub;

class ArgumentsResolver
{
    public function resolve($arguments)
    {
        if (null === $arguments) {
            return array();
        }

        return array_map(
            function($argument) {
                return $argument instanceof ObjectStub ? $argument->getStubSubject() : $argument;
            },
            (array) $arguments
        );
    }
}
