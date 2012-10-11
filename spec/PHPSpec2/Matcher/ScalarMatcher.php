<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\Example\FailureException;


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
        $this->supports('beScalar', '', array('string'))->shouldReturn(true);
        $this->supports('beScalar', '', array('float'))->shouldReturn(true);
        $this->supports('beScalar', '', array('int'))->shouldReturn(true);
        $this->supports('beScalar', '', array('boolean'))->shouldReturn(true);
        $this->supports('beScalar', '', array('array'))->shouldReturn(true);
        $this->supports('beScalar', '', array('resource'))->shouldReturn(true);
        $this->supports('beScalar', '', array('null'))->shouldReturn(true);
        $this->supports('beScalar', '', array('callable'))->shouldReturn(true);
    }

    public function it_should_not_support_types_that_is_not_primitive()
    {
        $this->supports('beScalar', '', array('\\stdClass'))->shouldReturn(false);
    }

    public function it_should_identify_string()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', '', array('string'));

        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', '', array('string'));
    }

    public function it_should_identify_boolean()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', true, array('boolean'));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', true, array('boolean'));
    }

    public function it_should_identify_int()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 42, array('int'));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 42, array('int'));
    }

    public function it_should_identify_float()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 42.0, array('float'));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 42.0, array('float'));
    }

    public function it_should_identify_array()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', array(), array('array'));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', array(), array('array'));
    }

    public function it_should_identify_null()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', null, array('null'));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', null, array('null'));
    }

    public function it_should_identify_resource()
    {
        $resource = fopen(__FILE__, 'r');

        $this->shouldNotThrow()->duringPositiveMatch('beScalar', $resource, array('resource'));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', $resource, array('resource'));
    }

    public function it_should_identify_callable()
    {
        $f = function() { return; };

        $this->shouldNotThrow()->duringPositiveMatch('beScalar', $f, array('callable'));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', $f, array('callable'));
    }

}
