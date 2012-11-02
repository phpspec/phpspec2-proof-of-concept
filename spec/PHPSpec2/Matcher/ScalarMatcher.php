<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;


class ScalarMatcher extends ObjectBehavior
{

    /**
     * @param PHPSpec2\Formatter\Presenter\StringPresenter $presenter
     */
    public function let($presenter)
    {
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val1');
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val2');

        $this->beConstructedWith($presenter);
    }

    public function it_should_support_all_aliases()
    {
        $this->supports('beInt', '', array())->shouldReturn(true);
        $this->supports('beFloat', '', array())->shouldReturn(true);
        $this->supports('beString', '', array())->shouldReturn(true);
        $this->supports('beBoolean', '', array())->shouldReturn(true);
    }

    public function it_should_not_support_non_scalar_types()
    {
        $this->supports('beArray', array(), array())->shouldReturn(false);
        $this->supports('beResource', fopen(__FILE__, 'r'), array())->shouldReturn(false);
        $this->supports('beCallable', function() {return;}, array())->shouldReturn(false);
    }

    public function it_throws_exception_when_type_doenst_match()
    {
        $this->shouldThrow(
            '\\PHPSpec2\\Exception\\Example\\FailureException'
        )->duringPositiveMatch('beString', true, array());
    }

    public function it_should_identify_string()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beString', '', array());
        $this->shouldThrow(
            '\\PHPSpec2\\Exception\\Example\\FailureException'
        )->duringNegativeMatch('beString', '', array());
    }

    public function it_should_identify_boolean()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beBoolean', true, array());
        $this->shouldThrow(
            '\\PHPSpec2\\Exception\\Example\\FailureException'
        )->duringNegativeMatch('beBoolean', true, array());
    }

    public function it_should_identify_int()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beInt', 42, array());
        $this->shouldThrow(
            '\\PHPSpec2\\Exception\\Example\\FailureException'
        )->duringNegativeMatch('beInt', 42, array());
    }

    public function it_should_identify_float()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beFloat', 42.0, array());
        $this->shouldThrow(
            '\\PHPSpec2\\Exception\\Example\\FailureException'
        )->duringNegativeMatch('beFloat', 42.0, array());
    }

}
