<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;

class TypeMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    function let($presenter)
    {
        $presenter->presentString(ANY_ARGUMENTS)->willReturnArgument();
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('object');

        $this->beConstructedWith($presenter);
    }

    function it_should_support_all_aliases()
    {
        $this->supports('beAnInstanceOf', '', array(''))->shouldReturn(true);
        $this->supports('returnAnInstanceOf', '', array(''))->shouldReturn(true);
        $this->supports('haveType', '', array(''))->shouldReturn(true);
    }

    /**
     * @param ArrayObject $object
     */
    function it_matches_subclass_instance($object)
    {
        $this->shouldNotThrow()
            ->duringPositiveMatch('haveType', $object, array('ArrayObject'));
    }

    /**
     * @param ArrayObject $object
     */
    function it_matches_interface_instance($object)
    {
        $this->shouldNotThrow()
            ->duringPositiveMatch('haveType', $object, array('ArrayAccess'));
    }

    /**
     * @param ArrayObject $object
     */
    function it_does_not_matches_wrong_class($object)
    {
        $this->shouldThrow(new FailureException(
            'Expected an instance of stdClass, but got object.'
        ))->duringPositiveMatch('haveType', $object, array('stdClass'));
    }

    /**
     * @param ArrayObject $object
     */
    function it_does_not_matches_wrong_interface($object)
    {
        $this->shouldThrow(new FailureException(
            'Expected an instance of SessionHandlerInterface, but got object.'
        ))->duringPositiveMatch('haveType', $object, array('SessionHandlerInterface'));
    }

    /**
     * @param ArrayObject $object
     */
    function it_mismatches_matches_wrong_class($object)
    {
        $this->shouldNotThrow()->duringNegativeMatch('haveType', $object, array('stdClass'));
    }

    function it_mismatches_matches_wrong_interface()
    {
        $this->shouldNotThrow()
            ->duringNegativeMatch('haveType', $this, array('SessionHandlerInterface'));
    }
}
