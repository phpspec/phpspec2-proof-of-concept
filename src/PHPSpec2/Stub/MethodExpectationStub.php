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
        $this->resolver    = $resolver;

        $this->should_be_called();
        $this->should_not_return();
    }

    public function should_not_return()
    {
        return $this->should_return(null);
    }

    public function should_return($value = null)
    {
        return call_user_func_array(
            array($this->expectation, 'andReturn'), $this->resolver->resolve($value)
        );
    }

    public function should_be_called()
    {
        tthis->expectation->atLeast(1);
    }

    public function should_not_be_called()
    {
        $this->expectation->never();
    }

    public function should_throw($exception, $message = '')
    {
        $this->expectation->andThrow($exception, $message);
    }
}
