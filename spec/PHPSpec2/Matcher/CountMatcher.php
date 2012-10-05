<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;

class CountMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    function let($presenter)
    {
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('countable');
        $presenter->presentString(ANY_ARGUMENTS)->willReturnArgument();
        $presenter->presentString(ANY_ARGUMENTS)->willReturnArgument();

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

    function it_should_not_match_wrong_array_count()
    {
        $this->shouldThrow(new FailureException('Expected countable to have 2 items, but got 3.'))
            ->duringPositiveMatch('haveCount', array(1,2,3), array(2));
    }

    /**
     * @param ArrayObject $countable
     */
    function it_should_not_match_proper_countable_count($countable)
    {
        $countable->count()->willReturn(5);

        $this->shouldThrow(new FailureException('Expected countable to have 4 items, but got 5.'))
            ->duringPositiveMatch('haveCount', $countable, array(4));
    }

    function it_should_mismatch_wrong_array_count()
    {
        $this->shouldNotThrow()->duringNegativeMatch('haveCount', array(1,2,3), array(2));
    }

    /**
     * @param ArrayObject $countable
     */
    function it_should_mismatch_wrong_countable_count($countable)
    {
        $countable->count()->willReturn(5);

        $this->shouldNotThrow()->duringNegativeMatch('haveCount', $countable, array(4));
    }
}
