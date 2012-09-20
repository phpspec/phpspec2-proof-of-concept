<?php

namespace PHPSpec2\Mocker;

use Mockery;

class MockeryMocker implements MockerInterface
{
    public function mock($classOrInterface)
    {
        $mock = Mockery::mock($classOrInterface);
        $mock->shouldIgnoreMissing();

        return $mock;
    }

    public function createExpectation($mock, $method, array $arguments = null)
    {
        if ($expectation = $this->getExpectation($mock, $method, $arguments)) {
            if (!$expectation->isCallCountConstrained()) {
                $expectation->atMost()->times(1);
            }
        }

        $expectation = $mock->shouldReceive($method);
        $this->withArguments($expectation, $arguments);

        return $expectation;
    }

    public function hasExpectation($mock, $method, array $arguments = null, $offset = null)
    {
        return null !== $this->getExpectation($mock, $method, $arguments, $offset);
    }

    public function getExpectation($mock, $method, array $arguments = null, $offset = null)
    {
        if (null === $director = $mock->mockery_getExpectationsFor($method)) {
            return;
        }

        $expectations = $director->getExpectations();

        if (null !== $arguments) {
            $expectations = array_filter($expectations, function($expectation) use($arguments) {
                return $expectation->matchArgs($arguments);
            });
            $expectations = array_values($expectations);
        }

        if (0 == $count = count($expectations)) {
            return;
        }

        if (null !== $offset) {
            if ($offset >= 0 && isset($expectations[$offset])) {
                return $expectations[$offset];
            }

            if ($offset < 0 && isset($expectations[$count + $offset])) {
                return $expectations[$count + $offset];
            }

            return;
        }

        return end($expectations);
    }

    public function makeDefault($expectation)
    {
        $expectation->byDefault();
    }

    public function shouldBeCalled($expectation)
    {
        $expectation->atLeast()->once();
    }

    public function shouldNotBeCalled($expectation)
    {
        $expectation->never();
    }

    public function shouldBeCalledTimes($expectation, $times)
    {
        $expectation->times($times);
    }

    public function withArguments($expectation, array $arguments = null)
    {
        if (null === $arguments) {
            return;
        }

        if (0 == count($arguments)) {
            $expectation->withNoArgs();

            return;
        }

        if (is_string($arguments[0]) && '__phpspec2_any_args__' === $arguments[0]) {
            $expectation->withAnyArgs();

            return;
        }

        call_user_func_array(array($expectation, 'with'), $arguments);
    }

    public function willReturn($expectation, $return)
    {
        if (is_callable($return)) {
            $expectation->andReturnUsing($return);
        } else {
            $expectation->andReturn($return);
        }
    }

    public function willThrow($expectation, $exception, $message = '')
    {
        $expectation->andThrow($exception, $message);
    }

    public function verify()
    {
        Mockery::close();
    }
}
