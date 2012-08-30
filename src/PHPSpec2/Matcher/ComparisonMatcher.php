<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Exception\Example\FailureException;

class ComparisonMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('beLike'))
            && 1 == count($arguments)
        ;
    }

    protected function matches($subject, array $arguments)
    {
        return $subject == $arguments[0];
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new NotEqualException('%s is not equal to expected %s, but should be.',
            $subject, $arguments[0]
        );
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(ucfirst(sprintf('%s is equal to %s, but it should not be.',
            gettype($subject), gettype($arguments[0])
        )));
    }
}
