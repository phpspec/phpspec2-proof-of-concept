<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;

use Countable;

class CountMatcher extends BasicMatcher
{
    public function supports($subject, $keyword)
    {
        return in_array($keyword, array('contain', 'have'));
    }

    protected function matches($subject, array $parameters)
    {
        if (isset($parameters[1])) {
            $getter  = 'get'.ucfirst($parameters[1]);
            $subject = $subject->$getter();
        }

        return intval($parameters[0]) == count($subject);
    }

    protected function getFailureException($subject, array $parameters)
    {
        return new ExampleException(sprintf(
            'Expected to have %d items in %s, got %d',
            $parameters[0],
            gettype($subject),
            count($subject)
        ));
    }

    protected function getNegativeFailureException($subject, array $parameters)
    {
        return new ExampleException(sprintf(
            'Expected to not have %d items in %s, got',
            $parameters[0],
            count($subject)
        ));
    }
}
