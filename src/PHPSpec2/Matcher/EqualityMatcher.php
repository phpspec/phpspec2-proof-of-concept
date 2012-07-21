<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;

class EqualityMatcher implements MatcherInterface
{
    public function supports($subject, $keyword, array $arguments)
    {
        return in_array($keyword, array('eql', 'equal', 'be_equal', 'equal_to', 'be_equal_to'))
            && 1 == count($arguments);
    }

    public function positive($subject, array $arguments)
    {
        if ($arguments[0] != $subject) {
            throw new ExampleException(sprintf(
                '%s and %s are not equal, but should be',
                gettype($arguments[0]),
                gettype($subject)
            ));
        }
    }

    public function negative($subject, array $arguments)
    {
        if ($arguments[0] == $subject) {
            throw new ExampleException(sprintf(
                '%s and %s are equal, but should not be',
                gettype($arguments[0]),
                gettype($subject)
            ));
        }
    }
}
