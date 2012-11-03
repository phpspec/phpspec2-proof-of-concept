<?php

namespace PHPSpec2\Mocker;

use Mockery;
use Mockery\CompositeExpectation;
use Mockery\CountValidator\Exception as MockeryCountException;

use PHPSpec2\Exception\Example\MockerException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

use ReflectionProperty;
use Mockery\Expectation;

class MockeryMocker implements MockerInterface
{
    private $presenter;

    public function __construct(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }

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
        if ($expectation instanceof CompositeExpectation) {
            $expectations = new ReflectionProperty($expectation, '_expectations');
            $expectations->setAccessible(true);

            foreach ($expectations->getValue($expectation) as $subExpectation) {
                $this->clearExpectationValidators($subExpectation);
            }
        } else {
            $this->clearExpectationValidators($expectation);
        }

        $expectation->atLeast()->once();
    }

    public function shouldNotBeCalled($expectation)
    {
        if ($expectation instanceof CompositeExpectation) {
            $expectations = new ReflectionProperty($expectation, '_expectations');
            $expectations->setAccessible(true);

            foreach ($expectations->getValue($expectation) as $subExpectation) {
                $this->clearExpectationValidators($subExpectation);
            }
        } else {
            $this->clearExpectationValidators($expectation);
        }

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

        if (is_string($arguments[0]) && ANY_ARGUMENTS === $arguments[0]) {
            $expectation->withAnyArgs();

            return;
        }

        $arguments = array_map(function($arg) {
            if (is_string($arg) && ANY_ARGUMENT === $arg) {
                return Mockery::any();
            } else {
                return $arg;
            }
        }, $arguments);

        call_user_func_array(array($expectation, 'with'), $arguments);
    }

    public function willReturn($expectation, $return)
    {
        $expectation->andReturn($return);
    }

    public function willReturnUsing($expectation, $callback)
    {
        $expectation->andReturnUsing($callback);
    }

    public function willThrow($expectation, $exception, $message = '')
    {
        $expectation->andThrow($exception, $message);
    }

    public function verify()
    {
        try {
            Mockery::close();
        } catch (MockeryCountException $e) {
            $message = $e->getMessage();

            if (preg_match(
                '/^Method ([^ ]+) from ([^ ]+).*(at least|at most|exactly).*(\d+).*(\d+)/smi',
                $message,
                $matches
            )) {
                $message = sprintf(
                    "Method <value>%s::%s</value>\nshould be called %s %d time(s), but called %d.",
                    $matches[2], $matches[1], $matches[3], $matches[4], $matches[5]
                );
            }

            throw new MockerException($message);
        }
    }

    private function clearExpectationValidators(Expectation $expectation)
    {
        $property = new ReflectionProperty($expectation, '_countValidators');
        $property->setAccessible(true);
        $property->setValue($expectation, array());
    }
}
