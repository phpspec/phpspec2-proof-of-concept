<?php

namespace PHPSpec2\Wrapper;

use Mockery\Undefined;

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
        if (is_array($argument)) {
            return array_map(array($this, 'unwrapOne'), $argument);
        }

        if (!is_object($argument)) {
            return $argument;
        }

        if ($argument instanceof SubjectWrapperInterface) {
            $argument = $argument->getWrappedSubject();
        }

        if ($argument instanceof Undefined) {
            return null;
        }

        return $argument;
    }
}
