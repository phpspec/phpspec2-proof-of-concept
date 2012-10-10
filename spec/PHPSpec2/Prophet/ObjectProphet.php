<?php

namespace spec\PHPSpec2\Prophet;

use PHPSpec2\ObjectBehavior;

class ObjectProphet extends ObjectBehavior
{
    /**
     * @param \ArrayAccess                         $subject
     * @param \PHPSpec2\Matcher\MatchersCollection $matchersCollection
     * @param \PHPSpec2\Wrapper\ArgumentsUnwrapper $unwrapper
     */
    function let($subject, $matchersCollection, $unwrapper)
    {
        $this->beConstructedWith($subject, $matchersCollection, $unwrapper);
    }

    function it_supports_get_array_access($subject)
    {
        $subject['foo']->willReturn('bar');

        $this['foo']->shouldReturn('bar');
    }

    function it_supports_set_array_access($subject)
    {
        $subject->offsetSet('foo', 'bar')->shouldBeCalled();

        $this['foo'] = 'bar';
    }

    function it_supports_exists_array_access($subject)
    {
        $subject->offsetExists('foo')->willReturn(true)->shouldBeCalled();

        $this->offsetExists('foo')->shouldReturn(true);
    }

    function it_supports_unset_array_access($subject)
    {
        $subject->offsetUnset('foo')->shouldBeCalled();

        unset($this['foo']);
    }
}
