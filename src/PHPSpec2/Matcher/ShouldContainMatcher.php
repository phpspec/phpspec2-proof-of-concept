<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Stub;
use PHPSpec2\Exception\Matcher\MatcherException;

class ShouldContainMatcher implements MatcherInterface
{
    public function getAliases()
    {
        return array('should_contain', 'shouldContain');
    }

    public function match(Stub $stub, array $arguments)
    {
        if ($arguments[0] !== count($stub->getStubSubject())) {
            throw new MatcherException(sprintf(
                'Expected to have %d items in %s, got %d',
                $arguments[0],
                gettype($stub->getStubSubject()),
                count($stub->getStubSubject())
            ));
        }
    }
}
