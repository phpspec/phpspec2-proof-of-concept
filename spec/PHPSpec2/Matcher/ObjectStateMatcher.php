<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;

class ObjectStateMatcher extends ObjectBehavior
{
    private $something;

    function it_infers_matcher_alias_name_from_methods_prefixed_with_is()
    {
        $subject = new \ReflectionClass($this);

        $this->supports('beAbstract', $subject, array())->shouldReturnTrue();
    }

    function it_throws_exception_if_checker_method_not_found()
    {
        $subject = new \ReflectionClass($this);
        $this->supports('beSimple', $subject, array());

        $this->shouldThrow('PHPSpec2\Exception\MethodNotFoundException')
            ->during('positiveMatch', array('beSimple', $subject, array()));
    }

    function it_matches_if_state_checker_returns_true()
    {
        $subject = new \ReflectionClass($this);
        $this->supports('beCloneable', $subject, array());

        $this->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('beCloneable', $subject, array()));
    }

    function it_does_not_matches_if_state_checker_returns_false()
    {
        $subject = new \ReflectionClass($this);
        $this->supports('beFinal', $subject, array());

        $this->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('beFinal', $subject, array()));
    }

    function it_infers_matcher_alias_name_from_methods_prefixed_with_has()
    {
        $subject = new \ReflectionClass($this);

        $this->supports('haveProperty', $subject, array('something'))->shouldReturnTrue();
    }

    function it_throws_exception_if_has_checker_method_not_found()
    {
        $subject = new \ReflectionClass($this);
        $this->supports('haveAnything', $subject, array('str'));

        $this->shouldThrow('PHPSpec2\Exception\MethodNotFoundException')
            ->during('positiveMatch', array('haveAnything', $subject, array('str')));
    }

    function it_matches_if_has_checker_returns_true()
    {
        $subject = new \ReflectionClass($this);
        $this->supports('haveProperty', $subject, array('something'));

        $this->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('haveProperty', $subject, array('something')));
    }

    function it_does_not_matches_if_has_state_checker_returns_false()
    {
        $subject = new \ReflectionClass($this);
        $this->supports('haveProperty', $subject, array('other'));

        $this->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('haveProperty', $subject, array('other')));
    }
}
