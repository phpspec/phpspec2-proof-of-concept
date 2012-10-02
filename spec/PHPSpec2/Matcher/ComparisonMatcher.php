<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;

class ComparisonMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    function described_with($presenter)
    {
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val1');
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val2');

        $this->initializedWith($presenter);
    }

    function it_should_support_all_aliases()
    {
        $this->supports('beLike', '', array(''))->shouldReturn(true);
    }

    function it_matches_empty_string_using_comparison_operator()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beLike', '', array(''));
    }

    function it_matches_not_empty_string_using_comparison_operator()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beLike', 'chuck', array('chuck'));
    }

    function it_matches_empty_string_with_emptish_values_using_comparison_operator()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beLike', '', array(0));
    }

    function it_matches_zero_with_emptish_values_using_comparison_operator()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beLike', 0, array(''));
    }

    function it_matches_null_with_emptish_values_using_comparison_operator()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beLike', null, array(''));
    }

    function it_matches_false_with_emptish_values_using_comparison_operator()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beLike', false, array(''));
    }

    function it_does_not_match_non_empty_different_value()
    {
        $this->shouldThrow(new FailureException('Expected val1, but got val2.'))
            ->duringPositiveMatch('beLike', 'one_value', array('different_value'));
    }

    function it_mismatches_empty_string_using_comparison_operator()
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('beLike', '', array(''));
    }

    function it_mismatches_not_empty_string_using_comparison_operator($matcher)
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('beLike', 'chuck', array('chuck'));
    }

    function it_mismatches_empty_string_with_emptish_values_using_comparison_operator()
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('beLike', '', array(''));
    }

    function it_mismatches_zero_with_emptish_values_using_comparison_operator()
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('beLike', 0, array(''));
    }

    function it_mismatches_null_with_emptish_values_using_comparison_operator()
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('beLike', null, array(''));
    }

    function it_mismatches_false_with_emptish_values_using_comparison_operator()
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('beLike', false, array(''));
    }

    function it_mismatches_on_non_empty_different_value()
    {
        $this->shouldNotThrow()->duringNegativeMatch('beLike', 'one_value', array('another'));
    }
}
