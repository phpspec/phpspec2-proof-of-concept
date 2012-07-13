<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Stub\ObjectStub;
use PHPSpec2\Exception\Example\ExampleException;

class CountMatcher extends BipolarMatcher
{
    public function getPositiveAliases()
    {
        return array('should_contain', 'shouldContain');
    }

    public function getNegativeAliases()
    {
        return array('snould_not_contain', 'shouldNotContain');
    }

    public function positiveMatch(ObjectStub $stub, array $arguments)
    {
        if ($arguments[0] !== count($stub->getStubSubject())) {
            throw new ExampleException(sprintf(
                'Expected to have %d items in %s, got %d',
                $arguments[0],
                gettype($stub->getStubSubject()),
                count($stub->getStubSubject())
            ));
        }
    }

    public function negativeMatch(ObjectStub $stub, array $arguments)
    {
        if ($arguments[0] === count($stub->getStubSubject())) {
            throw new ExampleException(sprintf(
                'Expected to not have %d items in %s, got',
                $arguments[0],
                count($stub->getStubSubject())
            ));
        }
    }
}
