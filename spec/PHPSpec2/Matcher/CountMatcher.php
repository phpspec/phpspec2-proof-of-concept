<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;

class CountMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    function described_with($presenter)
    {
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('countable');
        $presenter->presentString(ANY_ARGUMENTS)->willReturn('count1');
        $presenter->presentString(ANY_ARGUMENTS)->willReturn('count2');

        $this->initializedWith($presenter);
    }

    function it_should_support_all_aliases()
    {
        $this->supports('haveCount', array(), array(''))->shouldReturn(true);
    }

    function it_should_match_proper_array_count()
    {
        $this->shouldNotThrow()->duringPositiveMatch('haveCount', array(1,2,3), array(3));
    }

    /**
     * @param ArrayObject $countable
     */
    function it_should_match_proper_countable_count($countable)
    {
        $countable->count()->willReturn(4);

        $this->shouldNotThrow()->duringPositiveMatch('haveCount', $countable, array(4));
    }
}
