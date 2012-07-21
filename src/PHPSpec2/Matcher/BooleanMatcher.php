<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\ExampleException;

class BooleanMatcher extends BasicMatcher
{
    public function supports($subject, $keyword)
    {
        return in_array($keyword, array('be'))
            && is_object($subject)
        ;
    }

    protected function matches($subject, array $parameters)
    {
        return (bool) call_user_func(array($subject, 'is'.ucfirst($parameters[0])));
    }

    protected function getFailureException($subject, array $parameters)
    {
        return new ExampleException(sprintf(
            '%s expected to be %s, but it is not',
            gettype($subject),
            $parameters[0]
        ));
    }

    protected function getNegativeFailureException($subject, array $parameters)
    {
        return new ExampleException(sprintf(
            '%s not expected to be %s, but it is',
            gettype($subject),
            $parameters[0]
        ));
    }
}
