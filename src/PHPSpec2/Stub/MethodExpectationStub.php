<?php

namespace PHPSpec2\Stub;

use Mockery\CompositeExpectation;

class MethodExpectationStub
{
    private $expectation;
    private $resolver;

    public function __construct(CompositeExpectation $expectation, ArgumentsResolver $resolver, array $arguments = array())
    {
        $this->expectation = call_user_func_array(array($expectation, 'with'), $arguments);
        $this->resolver = $resolver;
        $this->shouldBeCalled();
        $this->shouldReturn(null);
    }

    public function should_return($value = null)
    {
        $this->shouldReturn($value);
    }

    public function should_be_called()
    {
        $this->shouldBeCalled();
    }

    public function should_not_be_called()
    {
        $this->shouldNotBeCalled();
    }

    public function should_throw($exception, $message = '')
    {
        $this->shouldThrow($exception, $message);
    }

    public function shouldReturn($value = null)
    {
        return call_user_func_array(array($this->expectation, 'andReturn'), $this->resolver->resolve($value));
    }

    public function shouldBeCalled()
    {
        $this->expectation->atLeast(1);
    }

    public function shouldNotBeCalled()
    {
        $this->expectation->never();
    }

    public function shouldThrow($exception, $message = '')
    {
        $this->expectation->andThrow($exception, $message);
    }
}
