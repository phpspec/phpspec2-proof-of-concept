<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;

class BooleanMatcher implements MatcherInterface
{
    public function supports($subject, $keyword, array $arguments)
    {
        return in_array($keyword, array('be'))
            && is_object($subject)
            && 1 == count($arguments);
    }

    public function positive($subject, array $arguments)
    {
        $checker = 'is'.ucfirst($arguments[0]);

        if ($subject->$checker()) {
            throw new ExampleException(sprintf(
                '%s expected to be %s, but it is not',
                gettype($subject),
                $arguments[0]
            ));
        }
    }

    public function negative($subject, array $arguments)
    {
        $checker = 'is'.ucfirst($arguments[0]);

        if ($subject->$checker()) {
            throw new ExampleException(sprintf(
                '%s not expected to be %s, but it is',
                gettype($subject),
                $arguments[0]
            ));
        }
    }
}
