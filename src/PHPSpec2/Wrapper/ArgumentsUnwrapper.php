<?php

namespace PHPSpec2\Wrapper;

use PHPSpec2\Wrapper\MockProxyInterface;

class ArgumentsUnwrapper
{
    public function unwrapAll(array $arguments)
    {
        if (null === $arguments) {
            return array();
        }

        return array_map(array($this, 'unwrapOne'), $arguments);
    }

    public function unwrapOne($argument)
    {
        if (!is_object($argument)) {
            return $argument;
        }

        while ($argument instanceof SubjectWrapperInterface) {
            $argument = $argument->getWrappedSubject();
        }

        return $argument;
    }
}
