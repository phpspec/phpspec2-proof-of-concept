<?php

namespace Spec\PHPSpec\PHPSpec2;

use PHPSpec\PHPSpec2\SpecificationInterface;

class Stub implements SpecificationInterface
{
    public function described_with($stub)
    {
        $stub->isAnInstanceOf('PHPSpec\PHPSpec2\Stub');
    }

    public function registers_matcher_if_it_has_aliases($stub, $matcher)
    {
        $matcher->isAMockOf('PHPSpec\PHPSpec2\Matcher\MatcherInterface');
        $matcher->getAliases()->should_return(array('should_be_equal'));

        $stub->__registerMatcher($matcher);
        $stub->__getMatchers()->should_contain(1);
    }

    public function does_not_registers_matcher_if_it_has_no_aliases($stub, $matcher)
    {
        $matcher->isAMockOf('PHPSpec\PHPSpec2\Matcher\MatcherInterface');
        $matcher->getAliases()->should_return(array());

        $stub->__registerMatcher($matcher);
        $stub->__getMatchers()->should_contain(0);
    }
}
