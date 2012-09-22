<?php

namespace PHPSpec2\Mocker;

use PHPSpec2\Wrapper\SubjectWrapperInterface;
use PHPSpec2\Wrapper\ArgumentsResolver;

class MockBehavior implements SubjectWrapperInterface
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

    public function getWrappedSubject()
    {
        return $this->subject;
    }
}
