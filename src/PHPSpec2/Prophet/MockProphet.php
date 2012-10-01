<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Mocker\MockExpectation;

use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class MockProphet implements ProphetInterface
{
    private $subject;
    private $mocker;
    private $unwrapper;

    public function __construct($subject = null, MockerInterface $mocker,
                                ArgumentsUnwrapper $unwrapper)
    {
        $this->subject   = $subject;
        $this->mocker    = $mocker;
        $this->unwrapper = $unwrapper;
    }

    public function isAMockOf($classOrInterface)
    {
        $this->subject = $this->mocker->mock($classOrInterface);
    }

    public function __get($method)
    {
        return new MockExpectation($this->subject, $method, $this->mocker, $this->unwrapper);
    }

    public function __call($method, array $arguments)
    {
        return call_user_func_array($this->$method, $arguments);
    }

    public function getWrappedSubject()
    {
        return $this->subject;
    }
}
