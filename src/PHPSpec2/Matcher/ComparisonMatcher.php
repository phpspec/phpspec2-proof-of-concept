<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\StringsNotEqualException;
use PHPSpec2\Exception\Example\ObjectsNotEqualException;
use PHPSpec2\Exception\Example\IntegersNotEqualException;
use PHPSpec2\Exception\Example\ArraysNotEqualException;
use PHPSpec2\Exception\Example\BooleansNotEqualException;
use PHPSpec2\Exception\Example\ResourcesNotEqualException;
use PHPSpec2\Exception\Example\FailureException;

class ComparisonMatcher extends BasicMatcher
{
    public function supports($name, $subject, array $arguments)
    {
        return in_array($name, array('be_like'));
    }

    protected function matches($name, $subject, array $arguments)
    {
        return $subject == $arguments[0];
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        if (is_object($subject)) {
            return new ObjectsNotEqualException(
                'Objects are not equal, but should be',
                $subject, $arguments[0]
            );
        } elseif (is_integer($subject)) {
            return new IntegersNotEqualException(
                'Integers are not equal, but should be',
                $subject, $arguments[0]
            );
        } elseif (is_array($subject)) {
            return new ArraysNotEqualException(
                'Arrays are not equal, but should be',
                $subject, $arguments[0]
            );
        } elseif (is_bool($subject)) {
            return new BooleansNotEqualException(
                'Booleans are not equal, but should be',
                $subject, $arguments[0]
            );
        } elseif (is_resource($subject)) {
            return new ResourcesNotEqualException(
                'Resources are not equal, but should be',
                $subject, $arguments[0]
            );
        } else {
            return new StringsNotEqualException(
                'Strings are not equal, but should be',
                $subject, $arguments[0]
            );
        }
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        if (is_object($subject)) {
            return new FailureException(
                'Objects are equal, but they shouldn\'t be'
            );
        } else {
            return new FailureException(
                'Strings are equal, but they shouldn\'t be'
            );
        }
    }
}
