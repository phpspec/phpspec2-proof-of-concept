<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use PHPSpec2\Exception\Example\MatcherNotFoundException;

class MatchersCollection implements Specification
{
    function it_will_complain_if_no_matchers_registered()
    {
        $this->object->shouldThrow(new MatcherNotFoundException('crazy_alias'))
            ->during('find', array('crazy_alias', 42, array()));
    }

    /**
     * @param ObjectStub $matcher mock of PHPSpec2\Matcher\MatcherInterface
     */
    function it_will_complain_if_matcher_is_not_found($matcher)
    {
        $this->object->add($matcher);
        $this->object->shouldThrow(new MatcherNotFoundException('crazy_alias'))
            ->during('find', array('crazy_alias', 42, array()));
    }

    /**
     * @param ObjectStub $matcher mock of PHPSpec2\Matcher\MatcherInterface
     */
    function it_will_return_matcher_if_found($matcher)
    {
        $matcher->supports('work', 42, array())->willReturn(true);

        $this->object->add($matcher);
        $this->object->find('work', 42, array())->shouldBeEqualTo($matcher);
    }
}
