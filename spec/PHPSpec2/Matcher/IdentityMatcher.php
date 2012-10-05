<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;

class IdentityMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    function let($presenter)
    {
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val1');
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val2');

        $this->beConstructedWith($presenter);
    }

    function it_should_support_all_aliases()
    {
        $this->supports('return', '', array(''))->shouldReturn(true);
        $this->supports('be', '', array(''))->shouldReturn(true);
        $this->supports('equal', '', array(''))->shouldReturn(true);
        $this->supports('beEqualTo', '', array(''))->shouldReturn(true);
    }

    function it_matches_empty_strings()
    {
        $this->shouldNotThrow()->duringPositiveMatch('be', '', array(''));
    }

    function it_matches_not_empty_strings()
    {
        $this->shouldNotThrow()->duringPositiveMatch('be', 'chuck', array('chuck'));
    }

    function it_does_not_matches_empty_string_with_emptish_values()
    {
        $this->shouldThrow(new FailureException('Expected val1, but got val2.'))
            ->duringPositiveMatch('be', '', array(false));
    }

    function it_does_not_matches_zero_with_emptish_values()
    {
        $this->shouldThrow(new FailureException('Expected val1, but got val2.'))
            ->duringPositiveMatch('be', 0, array(false));
    }

    function it_does_not_matches_null_with_emptish_values()
    {
        $this->shouldThrow(new FailureException('Expected val1, but got val2.'))
            ->duringPositiveMatch('be', null, array(false));
    }

    function it_does_not_matches_false_with_emptish_values()
    {
        $this->shouldThrow(new FailureException('Expected val1, but got val2.'))
            ->duringPositiveMatch('be', false, array(''));
    }

    function it_does_not_matches_non_empty_different_value()
    {
        $this->shouldThrow(new FailureException('Expected val1, but got val2.'))
            ->duringPositiveMatch('be', 'one', array('two'));
    }

    function it_mismatches_empty_string()
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('be', '', array(''));
    }

    function it_mismatches_not_empty_string($matcher)
    {
        $this->shouldThrow(new FailureException('Not expected val1, but got one.'))
            ->duringNegativeMatch('be', 'chuck', array('chuck'));
    }

    function it_mismatches_empty_string_with_emptish_values()
    {
        $this->shouldNotThrow()->duringNegativeMatch('be', '', array(false));
    }

    function it_mismatches_zero_with_emptish_values_using_identity_operator()
    {
        $this->shouldNotThrow()->duringNegativeMatch('be', 0, array(false));
    }

    function it_mismatches_null_with_emptish_values_using_identity_operator()
    {
        $this->shouldNotThrow()->duringNegativeMatch('be', null, array(false));
    }

    function it_mismatches_false_with_emptish_values_using_identity_operator()
    {
        $this->shouldNotThrow()->duringNegativeMatch('be', false, array(''));
    }

    function it_mismatches_on_non_empty_different_value()
    {
        $this->shouldNotThrow()->duringNegativeMatch('be', 'one', array('two'));
    }
}
