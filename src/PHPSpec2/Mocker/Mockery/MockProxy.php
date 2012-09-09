<?php

namespace PHPSpec2\Mocker\Mockery;

use Mockery;

use PHPSpec2\Mocker\MockProxyInterface;
use PHPSpec2\Stub\ArgumentsResolver;

class MockProxy implements MockProxyInterface
{
    private $originalMock;

    public function __construct($classOrInterface)
    {
        $this->originalMock = Mockery::mock($classOrInterface);
        $this->originalMock->shouldIgnoreMissing();
    }

    public function getOriginalMock()
    {
        return $this->originalMock;
    }

    public function mockMethod($method, array $arguments, ArgumentsResolver $resolver)
    {
        if ($director = $this->originalMock->mockery_getExpectationsFor($method)) {
            $expectations = $director->getExpectations();
            $expectation = end($expectations);
        } else {
            $expectation = $this->originalMock->shouldReceive($method);
        }

        return new ExpectationProxy($expectation, $arguments, $resolver);
    }
}
