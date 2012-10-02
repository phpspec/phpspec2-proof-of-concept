<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;

class TypeMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    function described_with($presenter)
    {
        $presenter->presentString(ANY_ARGUMENTS)->willReturn('class');
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('object');

        $this->initializedWith($presenter);
    }

    function it_should_support_all_aliases()
    {
        $this->supports('beAnInstanceOf', '', array(''))->shouldReturn(true);
        $this->supports('returnAnInstanceOf', '', array(''))->shouldReturn(true);
        $this->supports('haveType', '', array(''))->shouldReturn(true);
    }

    function it_matches_class_instance()
    {
        $this->shouldNotThrow()
            ->duringPositiveMatch('haveType', $this, array('spec\PHPSpec2\Matcher\TypeMatcher'));
    }

    function it_matches_subclass_instance()
    {
        $this->shouldNotThrow()
            ->duringPositiveMatch('haveType', $this, array('PHPSpec2\ObjectBehavior'));
    }

    function it_matches_interface_instance()
    {
        $this->shouldNotThrow()
            ->duringPositiveMatch('haveType', $this, array('PHPSpec2\SpecificationInterface'));
    }

    function it_does_not_matches_wrong_class()
    {
        $this->shouldThrow(new FailureException('Expected an instance of class, but got object.'))
            ->duringPositiveMatch('haveType', $this, array('stdClass'));
    }

    function it_does_not_matches_wrong_interface()
    {
        $this->shouldThrow(new FailureException('Expected an instance of class, but got object.'))
            ->duringPositiveMatch('haveType', $this, array('SessionHandlerInterface'));
    }

    function it_mismatches_matches_wrong_class()
    {
        $this->shouldNotThrow()->duringNegativeMatch('haveType', $this, array('stdClass'));
    }

    function it_mismatches_matches_wrong_interface()
    {
        $this->shouldNotThrow()
            ->duringNegativeMatch('haveType', $this, array('SessionHandlerInterface'));
    }
}
