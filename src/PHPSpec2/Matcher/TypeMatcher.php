<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;

class TypeMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('be_an_instance_of', 'return_an_instance_of'));
    }

    protected function matches($subject, array $arguments)
    {
        return null !== $subject && $subject instanceof $arguments[0];
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        $actual = null === $subject ? 'null' : get_class($subject);

        return new FailureException(
            "Expected an instance of {$arguments[0]} but got " .
            $actual . " instead"
        );
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
    }
}
