<?php

namespace PHPSpec2\Mocker;

use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Exception\MockException;

use ArrayAccess;

class MockExpectation implements ArrayAccess
{
    private $mock;
    private $method;
    private $arguments;
    private $mocker;
    private $expectation;
    private $unwrapper;
    private $presenter;

    public function __construct($mock, $method, MockerInterface $mocker,
                                ArgumentsUnwrapper $unwrapper, PresenterInterface $presenter)
    {
        $this->mock      = $mock;
        $this->method    = $method;
        $this->mocker    = $mocker;
        $this->unwrapper = $unwrapper;
        $this->presenter = $presenter;
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

    public function willBeDefault()
    {
        $this->mocker->makeDefault($this->getExpectation());

        return $this;
    }

    public function willReturn($value)
    {
        $this->mocker->willReturn(
            $this->getExpectation(), $this->unwrapper->unwrapOne($value)
        );

        return $this;
    }

    public function willReturnUsing($callback)
    {
        $this->mocker->willReturnUsing($this->getExpectation(), $callback);

        return $this;
    }

    public function willReturnArgument()
    {
        $this->willReturnUsing(function($argument) { return $argument; });

        return $this;
    }

    public function willReturnSelf()
    {
        $this->willReturn($this->mock);

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
            throw new MockException(sprintf(
                'Expectation with %s offset not found.',
                $this->presenter->presentValue($offset)
            ));
        }

        $this->expectation = $this->mocker->getExpectation(
            $this->mock, $this->method, $this->arguments, $offset
        );

        return $this;
    }

    public function offsetUnset($offset)
    {
        throw new MockException('You can not unset already defined expectation.');
    }

    public function offsetSet($offset, $value)
    {
        throw new MockException('You can not replace already defined expectation.');
    }

    public function __invoke()
    {
        $this->arguments = $this->unwrapper->unwrapAll(func_get_args());
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
