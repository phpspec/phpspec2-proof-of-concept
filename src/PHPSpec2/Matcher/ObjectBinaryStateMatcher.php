<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Stub\MethodNotFoundException;
use PHPSpec2\Exception\Example\FailureException;

class ObjectBinaryStateMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return 'be' === $name
            && is_object($subject)
            && 1 == count($arguments)
        ;
    }

    protected function matches($subject, array $arguments)
    {
        $method = 'is'.ucfirst($arguments[0]);

        if (!method_exists($subject, $method)) {
            throw new MethodNotFoundException($subject, $method);
        }

        return (bool) $subject->$method();
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        $method = 'is'.ucfirst($arguments[0]);

        return new FailureException("Expected $method to return true, got false.");
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        $method = 'is'.ucfirst($arguments[0]);

        return new FailureException("Expected $method to return false, got true.");
    }
}
