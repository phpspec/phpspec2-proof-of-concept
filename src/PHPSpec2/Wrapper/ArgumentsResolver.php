<?php

namespace PHPSpec2\Wrapper;

use PHPSpec2\Wrapper\MockProxyInterface;

class ArgumentsResolver
{
    public function resolveAll(array $arguments)
    {
        if (null === $arguments) {
            return array();
        }

        return array_map(array($this, 'resolveSingle'), $arguments);
    }

    public function resolveSingle($argument)
    {
        if (!is_object($argument)) {
            return $argument;
        }

        if ($argument instanceof SubjectWrapperInterface) {
            $argument = $argument->getWrappedSubject();
        }

        return $argument;
    }
}
