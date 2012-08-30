<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class ObjectContainsMatcher implements Specification
{
    private $something;

    function it_infers_matcher_alias_name_from_methods_prefixed_with_has()
    {
        $subject = new \ReflectionClass($this);

        $this->object->supports('haveProperty', $subject, array('something'))->shouldReturnTrue();
    }

    function it_throws_exception_if_checker_method_not_found()
    {
        $subject = new \ReflectionClass($this);
        $this->object->supports('haveAnything', $subject, array('str'));

        $this->object->shouldThrow('PHPSpec2\Exception\Stub\MethodNotFoundException')
            ->during('positiveMatch', array('haveAnything', $subject, array('str')));
    }

    function it_matches_if_checker_returns_true()
    {
        $subject = new \ReflectionClass($this);
        $this->object->supports('haveProperty', $subject, array('something'));

        $this->object->shouldNotThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('haveProperty', $subject, array('something')));
    }

    function it_does_not_matches_if_state_checker_returns_false()
    {
        $subject = new \ReflectionClass($this);
        $this->object->supports('haveProperty', $subject, array('other'));

        $this->object->shouldThrow('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('haveProperty', $subject, array('other')));
    }
}
