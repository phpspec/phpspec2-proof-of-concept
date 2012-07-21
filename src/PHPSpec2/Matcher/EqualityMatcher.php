<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;

class EqualityMatcher extends BasicMatcher
{
    public function supports($subject, $keyword, array $parameters)
    {
        return in_array($keyword, array('eql', 'equal', 'be_equal', 'equal_to', 'be_equal_to'))
            && 1 == count($parameters);
    }

    protected function matches($subject, array $parameters)
    {
        return $parameters[0] == $subject;
    }

    protected function getFailureException($subject, array $parameters)
    {
        return new ExampleException(sprintf(
            '%s and %s are not equal, but should be',
            gettype($parameters[0]),
            gettype($subject)
        ));
    }

    protected function getNegativeFailureException($subject, array $parameters)
    {
        return new ExampleException(sprintf(
            '%s and %s are equal, but should not be',
            gettype($parameters[0]),
            gettype($subject)
        ));
    }
}
