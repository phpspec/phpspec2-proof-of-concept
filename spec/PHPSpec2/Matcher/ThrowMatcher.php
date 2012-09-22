<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use stdClass;

class ThrowMatcher extends ObjectBehavior
{
    function it_supports_the_throw_alias_for_object_and_exception_name()
    {
        $this
            ->supports('throw', new stdClass, array('\Exception'))
            ->shouldReturnTrue();
    }

    /**
     * @param Prophet $subject mock of stdClass
     */
    function it_can_specify_a_method_during_which_an_exception_should_be_throw($subject)
    {
        $subject->someMethod()
            ->willThrow('\Exception');

        $this
            ->positiveMatch('throw', $subject, array('\Exception'))
            ->during('someMethod', array());
    }

    /**
     * @param Prophet $subject mock of stdClass
     */
    function it_can_specify_a_method_during_which_an_exception_should_not_be_throw($subject)
    {
        $this
            ->negativeMatch('throw', $subject, array('\Exception'))
            ->during('someMethod', array());
    }
}
