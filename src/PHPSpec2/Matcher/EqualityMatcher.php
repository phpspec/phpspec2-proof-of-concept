<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Stub\ObjectStub;
use PHPSpec2\Exception\Example\ExampleException;

class EqualityMatcher extends BipolarMatcher
{
    public function getPositiveAliases()
    {
        return array(
            'should_equal', 'should_be_equal', 'shouldEqual', 'shouldBeEqual'
        );
    }

    public function getNegativeAliases()
    {
        return array(
            'should_not_equal', 'should_not_be_equal', 'shouldNotEqual', 'shouldNotBeEqual'
        );
    }

    public function positiveMatch(ObjectStub $stub, array $arguments)
    {
        if ($arguments[0] != $stub->getStubSubject()) {
            throw new ExampleException(sprintf(
                '%s and %s are not equal, but should be',
                gettype($arguments[0]),
                gettype($stub->getStubSubject())
            ));
        }
    }

    public function negativeMatch(ObjectStub $stub, array $arguments)
    {
        if ($arguments[0] == $stub->getStubSubject()) {
            throw new ExampleException(sprintf(
                '%s and %s are equal, but should not be',
                gettype($arguments[0]),
                gettype($stub->getStubSubject())
            ));
        }
    }
}
