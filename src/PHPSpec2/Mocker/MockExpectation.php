<?php

namespace PHPSpec2\Mocker;

use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Wrapper\ArgumentsResolver;

use ArrayAccess;

class MockExpectation implements ArrayAccess
{
    private $mock;
    private $method;
    private $arguments;
    private $mocker;
    private $expectation;
    private $resolver;

    public function __construct($mock, $method, MockerInterface $mocker,
                                ArgumentsResolver $resolver)
    {
        $this->mock     = $mock;
        $this->method   = $method;
        $this->mocker   = $mocker;
        $this->resolver = $resolver;
    }

    public function byDefault()
    {
        $this->mocker->makeDefault($this->getExpectation());

        return $this;
    }

    public function shouldBeCalled()
    {
        $this->mocker->shouldBeCalled($this->getExpectation());

        return $this;
    }

    public function shouldNotBeCalled()
    {
        $this->mocker->shouldNotBeCalled($this->getExpectation());

        return $this;
    }

    public function willReturn($value)
    {
        $this->mocker->willReturn(
            $this->getExpectation(), $this->resolver->resolveSingle($value)
        );

        return $this;
    }

    public function willThrow($exception, $message = '')
    {
        if ($exception instanceof \Exception) {
            $message   = $exception->getMessage();
            $exception = get_class($exception);
        }

        $this->mocker->willThrow($this->getExpectation(), $exception, $message);

        return $this;
    }

    public function offsetExists($offset)
    {
        return $this->mocker->hasExpectation(
            $this->mock, $this->method, $this->arguments, $offset
        );
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            // TODO: throw exception
        }

        $this->expectation = $this->mocker->getExpectation(
            $this->mock, $this->method, $this->arguments, $offset
        );

        return $this;
    }

    public function offsetUnset($offset)
    {
        // TODO: throw exception
    }

    public function offsetSet($offset, $value)
    {
        // TODO: throw exception
    }

    public function __invoke()
    {
        $this->arguments = $this->resolver->resolve(func_get_args());
        if (null !== $this->expectation) {
            $this->mocker->withArguments($this->expectation, $this->arguments);
        }

        return $this;
    }

    private function getExpectation()
    {
        if (null !== $this->expectation) {
            return $this->expectation;
        }

        return $this->expectation = $this->mocker->createExpectation(
            $this->mock, $this->method, $this->arguments
        );
    }
}
