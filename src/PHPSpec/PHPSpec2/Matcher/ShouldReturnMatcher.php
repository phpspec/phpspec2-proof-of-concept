<?php

namespace PHPSpec\PHPSpec2\Matcher;

use PHPSpec\PHPSpec2\Stub;

class ShouldReturnMatcher implements MatcherInterface
{
    public function getAliases()
    {
        return array('should_return', 'shouldReturn');
    }

    public function match(Stub $stub, array $arguments)
    {
        return new Stub(
            $stub->getSubject()->andReturn($arguments[0]),
            $stub->getMatchers()
        );
    }
}
