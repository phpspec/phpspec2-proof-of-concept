<?php

namespace Spec\PHPSpec2\Matcher;

use PHPSpec2\SpecificationInterface;

class MatchersCollection implements SpecificationInterface
{
    function described_with()
    {
        $this->object->is_an_instance_of('PHPSpec2\Matcher\MatchersCollection');
    }

    function will_complain_if_no_matchers_registered()
    {
        $this->object->should_throw('PHPSpec2\Exception\Example\MatcherNotFoundException')
                     ->during('find', array('crazy_alias', 42, array()));
    }

    function will_complain_if_matcher_is_not_found($matcher)
    {
        $matcher->is_a_mock_of('PHPSpec2\Matcher\MatcherInterface');

        $this->object->add($matcher);
        $this->object->should_throw('PHPSpec2\Exception\Example\MatcherNotFoundException')
                     ->during('find', array('crazy_alias', 42, array()));
    }
}
