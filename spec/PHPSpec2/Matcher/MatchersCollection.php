<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\MatcherNotFoundException;

class MatchersCollection extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\PresenterInterface $presenter
     */
    function let($presenter)
    {
        $presenter->presentValue(ANY_ARGUMENT)->willReturn('val');
        $presenter->presentString(ANY_ARGUMENT)->willReturn('string');

        $this->beConstructedWith($presenter);
    }

    function it_will_complain_if_no_matchers_registered()
    {
        $this->shouldThrow(new MatcherNotFoundException(
            'No string(val) matcher found for val.', 'crazy_alias', 42, array()
        ))->duringFind('crazy_alias', 42, array());
    }

    /**
     * @param PHPSpec2\Matcher\MatcherInterface $matcher
     */
    function it_will_complain_if_matcher_is_not_found($matcher)
    {
        $this->add($matcher);
        $this->shouldThrow(new MatcherNotFoundException(
            'No string(val) matcher found for val.', 'crazy_alias', 42, array()
        ))->duringFind('crazy_alias', 42, array());
    }

    /**
     * @param PHPSpec2\Matcher\MatcherInterface $matcher
     */
    function it_will_return_matcher_if_found($matcher)
    {
        $matcher->supports('work', 42, array())->willReturn(true);

        $this->add($matcher);
        $this->find('work', 42, array())->shouldReturn($matcher);
    }
}
