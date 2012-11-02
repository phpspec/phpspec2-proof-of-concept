<?php

namespace PHPSpec2\Prophet;

use ArrayAccess;

use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Mocker\MockExpectation;

use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class MockProphet implements ProphetInterface, ArrayAccess
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

    public function beAMockOf($classOrInterface)
    {
        $this->subject = $this->mocker->mock($classOrInterface);
    }

    public function offsetExists($offset)
    {
        return call_user_func_array($this->offsetExists, array($offset));
    }

    public function offsetGet($offset)
    {
        return call_user_func_array($this->offsetGet, array($offset));
    }

    public function offsetSet($offset, $value)
    {
        return call_user_func_array($this->offsetSet, array($offset, $value));
    }

    public function offsetUnset($offset)
    {
        return call_user_func_array($this->offsetUnset, array($offset));
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
