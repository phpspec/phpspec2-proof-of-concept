<?php

namespace PHPSpec2\Stub;

use PHPSpec2\Stub\Mocker\MockProxyInterface;

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
        if (null === $argument) {
            return $argument;
        }

        if ($argument instanceof ObjectStub) {
            $argument = $argument->getStubSubject();
        }

        if ($argument instanceof MockProxyInterface) {
            $argument = $argument->getOriginalMock();
        }

        return $argument;
    }
}
