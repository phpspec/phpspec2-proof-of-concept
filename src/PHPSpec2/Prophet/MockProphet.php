<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Mocker\MockExpectation;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class MockProphet implements ProphetInterface
{
    private $subject;
    private $mocker;
    private $unwrapper;
    private $presenter;

    public function __construct($subject = null, MockerInterface $mocker,
                                ArgumentsUnwrapper $unwrapper, PresenterInterface $presenter)
    {
        $this->subject   = $subject;
        $this->mocker    = $mocker;
        $this->unwrapper = $unwrapper;
        $this->presenter = $presenter;
    }

    public function beAMockOf($classOrInterface)
    {
        $this->subject = $this->mocker->mock($classOrInterface);
    }

    public function __set($property, $value)
    {
        if (null === $this->subject) {
            $this->beAMockOf('stdClass');
        }

        $this->getWrappedSubject()->$property = $this->unwrapper->unwrapOne($value);
    }

    public function __get($method)
    {
        if (property_exists($this->getWrappedSubject(), $method)) {
            return $this->getWrappedSubject()->$method;
        }

        if (null === $this->subject) {
            $this->beAMockOf('stdClass');
        }

        return $this->createMethodMockExpectation($method);
    }

    public function __call($method, array $arguments)
    {
        if (null === $this->subject) {
            $this->beAMockOf('stdClass');
        }

        return call_user_func_array($this->createMethodMockExpectation($method), $arguments);
    }

    public function getWrappedSubject()
    {
        return $this->subject;
    }

    protected function createMethodMockExpectation($method)
    {
        return new MockExpectation(
            $this->subject, $method, $this->mocker, $this->unwrapper, $this->presenter
        );
    }
}
