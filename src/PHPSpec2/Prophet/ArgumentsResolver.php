<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Mocker\MockProxyInterface;
use Mockery\MockInterface;

class ArgumentsResolver
{
    public function resolve(array $arguments)
    {
        if (null === $arguments) {
            return array();
        }

        return array_map(array($this, 'resolveSingle'), $arguments);
    }

    public function resolveSingle($argument)
    {
        if (!is_object($argument) || $argument instanceof MockInterface) {
            return $argument;
        }

        if ($argument instanceof ProphetInterface) {
            $argument = $argument->getProphetSubject();
        }

        return $argument;
    }
}
