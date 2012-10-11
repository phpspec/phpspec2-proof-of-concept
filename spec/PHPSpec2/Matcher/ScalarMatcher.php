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
        $this->supports('beScalar', 'string', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'float', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'int', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'boolean', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'array', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'object', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'resource', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'null', array(''))->shouldReturn(true);
        $this->supports('beScalar', 'callable', array(''))->shouldReturn(true);
    }

    public function it_should_not_support_types_that_is_not_primitive()
    {
        $this->supports('beScalar', '\\stdClass', array(''))->shouldReturn(false);
    }

    public function it_should_identify_string()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'string', array(''));

        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'string', array(''));
    }

    public function it_should_identify_boolean()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'boolean', array(true));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'boolean', array(true));
    }

    public function it_should_identify_int()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'int', array(42));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'int', array(42));
    }

    public function it_should_identify_float()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'float', array(42.0));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'float', array(42.0));
    }

    public function it_should_identify_array()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'array', array(array()));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'array', array(array()));
    }

    public function it_should_identify_object()
    {
        $obj = new \stdClass();

        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'object', array($obj));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'object', array($obj));
    }

    public function it_should_identify_null()
    {
        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'null', array(null));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'null', array(null));
    }

    public function it_should_identify_resource()
    {
        $resource = fopen(__FILE__, 'r');

        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'resource', array($resource));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'resource', array($resource));
    }

    public function it_should_identify_callable()
    {
        $f = function() { return; };

        $this->shouldNotThrow()->duringPositiveMatch('beScalar', 'callable', array($f));
        $this->shouldThrow(
            new FailureException('Not expected val1, but got one.')
        )->duringNegativeMatch('beScalar', 'callable', array($f));
    }

}
