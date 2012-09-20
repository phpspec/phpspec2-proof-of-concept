<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Mocker\MockExpectation;

class MockProphet implements ProphetInterface
{
    private $subject;
    private $mocker;
    private $resolver;

    public function __construct($subject = null, MockerInterface $mocker,
                                ArgumentsResolver $resolver)
    {
        $this->subject  = $subject;
        $this->mocker   = $mocker;
        $this->resolver = $resolver;
    }

    public function isAMockOf($classOrInterface)
    {
        $this->subject = $this->mocker->mock($classOrInterface);
    }

    public function __get($method)
    {
        return new MockExpectation($this->subject, $method, $this->mocker, $this->resolver);
    }

    public function __call($method, array $arguments)
    {
        return call_user_func_array($this->$method, $arguments);
    }

    public function getProphetSubject()
    {
        return $this->subject;
    }
}
