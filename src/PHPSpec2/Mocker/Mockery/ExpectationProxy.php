<?php

namespace PHPSpec2\Mocker\Mockery;

use Mockery\CompositeExpectation;
use PHPSpec2\Stub\ArgumentsResolver;

class ExpectationProxy
{
    private $expectation;
    private $resolver;

    public function __construct(CompositeExpectation $expectation, array $arguments,
                                ArgumentsResolver $resolver)
    {
        $this->expectation = call_user_func_array(array($expectation, 'with'), $arguments);
        $this->resolver    = $resolver;

        $this->willReturn(null);
    }

    public function willReturn($value = null)
    {
        return call_user_func_array(
            array($this->expectation, 'andReturn'), $this->resolver->resolve($value)
        );

        return $this;
    }

    public function shouldBeCalled()
    {
        $this->expectation->atLeast()->once();

        return $this;
    }

    public function shouldNotBeCalled()
    {
        $this->expectation->never();

        return $this;
    }

    public function willThrow($exception, $message = '')
    {
        $this->expectation->andThrow($exception, $message);

        return $this;
    }
}
