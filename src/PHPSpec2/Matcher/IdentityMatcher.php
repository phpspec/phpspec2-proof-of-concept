<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;

class IdentityMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('return', 'be', 'equal', 'beEqualTo'))
            && 1 == count($arguments)
        ;
    }

    protected function matches($subject, array $arguments)
    {
        return $subject === $arguments[0];
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new FailureException(ucfirst(sprintf(
            '%s is not the same as expected %s, but it should be.',
            gettype($subject), gettype($arguments[0])
        )));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(ucfirst(sprintf(
            '%s is the same as expected %s, but it should not be.',
            gettype($subject), gettype($arguments[0])
        )));
    }
}
