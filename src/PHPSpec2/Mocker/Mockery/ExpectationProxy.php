<?php

namespace PHPSpec2\Mocker\Mockery;

use PHPSpec2\Prophet\ArgumentsResolver;

class ExpectationProxy
{
    private $expectation;
    private $resolver;

    public function __construct($expectation, array $arguments, ArgumentsResolver $resolver)
    {
        $this->expectation = $expectation;
        $this->resolver    = $resolver;

        $this->withArguments($arguments);
        $this->willReturn(null);
    }

    public function withArguments(array $arguments)
    {
        call_user_func_array(
            array($this->expectation, 'with'), $this->resolver->resolve($arguments)
        );

        return $this;
    }

    public function willReturn($value = null)
    {
        $this->expectation->andReturn($this->resolver->resolveSingle($value));

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
