<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use PHPSpec2\Exception\Example\MatcherNotFoundException;

class MatchersCollection implements Specification
{
    function described_with($matcher)
    {
        $this->object->is_an_instance_of('PHPSpec2\Matcher\MatchersCollection');
        $matcher->is_a_mock_of('PHPSpec2\Matcher\MatcherInterface');
    }

    function will_complain_if_no_matchers_registered()
    {
        $this->object
            ->should_throw(new MatcherNotFoundException('crazy_alias'))
            ->during('find', array('crazy_alias', 42, array()));
    }

    function will_complain_if_matcher_is_not_found($matcher)
    {
        $this->object->add($matcher);
        $this->object
            ->should_throw('PHPSpec2\Exception\Example\MatcherNotFoundException')
            ->during('find', array('crazy_alias', 42, array()));
    }

    function will_return_matcher_if_found($matcher)
    {
        $matcher->supports('work', 42, array())->should_return(true);

        $this->object->add($matcher);
        $this->object->find('work', 42, array())->should_equal($matcher);
    }
}
