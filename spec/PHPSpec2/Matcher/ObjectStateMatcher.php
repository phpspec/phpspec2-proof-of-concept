<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;

class ObjectStateMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\ValuePresenter $presenter
     */
    function let($presenter)
    {
        $presenter->presentString(ANY_ARGUMENTS)->willReturn('str1');
        $presenter->presentString(ANY_ARGUMENTS)->willReturn('str2');
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val1');
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val2');

        $this->beConstructedWith($presenter);
    }

    function it_infers_matcher_alias_name_from_methods_prefixed_with_is()
    {
        $subject = new \ReflectionClass($this);

        $this->supports('beAbstract', $subject, array())->shouldReturn(true);
    }

    function it_throws_exception_if_checker_method_not_found()
    {
        $subject = new \ReflectionClass($this);

        $this->shouldThrow('PHPSpec2\Exception\MethodNotFoundException')
            ->duringPositiveMatch('beSimple', $subject, array());
    }

    function it_matches_if_state_checker_returns_true()
    {
        $subject = new \ReflectionClass($this);

        $this->shouldNotThrow()->duringPositiveMatch('beUserDefined', $subject, array());
    }

    function it_does_not_matches_if_state_checker_returns_false()
    {
        $subject = new \ReflectionClass($this);

        $this->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->duringPositiveMatch('beFinal', $subject, array());
    }

    function it_infers_matcher_alias_name_from_methods_prefixed_with_has()
    {
        $subject = new \ReflectionClass($this);

        $this->supports('haveProperty', $subject, array('something'))->shouldReturn(true);
    }

    function it_throws_exception_if_has_checker_method_not_found()
    {
        $subject = new \ReflectionClass($this);

        $this->shouldThrow('PHPSpec2\Exception\MethodNotFoundException')
            ->duringPositiveMatch('haveAnything', $subject, array('str'));
    }

    function it_matches_if_has_checker_returns_true()
    {
        $subject = new \ReflectionClass($this);

        $this->shouldNotThrow()->duringPositiveMatch(
            'haveMethod', $subject, array('it_matches_if_has_checker_returns_true')
        );
    }

    function it_does_not_matches_if_has_state_checker_returns_false()
    {
        $subject = new \ReflectionClass($this);

        $this->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->duringPositiveMatch('haveProperty', $subject, array('other'));
    }
}
