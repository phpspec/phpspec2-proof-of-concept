<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Mocker\MockProxyInterface;

class ArgumentsResolver
{
    public function resolve($arguments)
    {
        if (null === $arguments) {
            return array();
        }

        return array_map(array($this, 'resolveSingle'), (array)$arguments);
    }

    public function resolveSingle($argument)
    {
        if (null === $argument || !is_object($argument)) {
            return $argument;
        }

        if ($argument instanceof Prophet) {
            $argument = $argument->getStubSubject();
        }

        if ($argument instanceof MockProxyInterface) {
            $argument = $argument->getOriginalMock();
        }

        return $argument;
    }
}
