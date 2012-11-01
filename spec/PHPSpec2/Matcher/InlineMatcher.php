<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\PendingException;
use PHPSpec2\Exception\Example\FailureException;

class InlineMatcher extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('haveResponse', function(){});
    }

    function it_should_be_matcher()
    {
        $this->shouldBeAnInstanceOf('PHPSpec2\Matcher\MatcherInterface');
    }

    function it_should_not_accept_non_callables_as_checker()
    {
        $this->shouldThrow()->during('__construct', array('name', 'checker'));
    }

    function it_should_not_support_any_alias()
    {
        $this->supports('any', 'subj', array())->shouldReturn(false);
    }

    function it_should_support_proper_alias()
    {
        $this->supports('haveResponse', 'subj', array())->shouldReturn(true);
    }

    function it_should_throw_exception_during_failed_positive_match()
    {
        $this->beConstructedWith('haveResponse', function($subject, $argument) {
            return false;
        });

        $this->shouldThrow(
            new FailureException('Subject expected to `haveResponse`, but it is not.')
        )->duringPositiveMatch('haveResponse', 'object', array('arg1'));
    }

    function it_should_not_throw_exception_during_successfull_positive_match()
    {
        $this->beConstructedWith('haveResponse', function($subject, $argument) {
            return true;
        });

        $this->shouldNotThrow()->duringPositiveMatch('haveResponse', 'object', array('arg1'));
    }

    function it_should_throw_exception_during_failed_negative_match()
    {
        $this->beConstructedWith('haveResponse', function($subject, $argument) {
            return true;
        });

        $this->shouldThrow(
            new FailureException('Subject expected not to `haveResponse`, but it is.')
        )->duringNegativeMatch('haveResponse', 'object', array('arg1'));
    }

    function it_should_not_throw_exception_during_successfull_negative_match()
    {
        $this->beConstructedWith('haveResponse', function($subject, $argument) {
            return false;
        });

        $this->shouldNotThrow()->duringNegativeMatch('haveResponse', 'object', array('arg1'));
    }
}
