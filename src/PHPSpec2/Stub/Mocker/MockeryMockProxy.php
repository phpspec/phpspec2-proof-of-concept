<?php

namespace PHPSpec2\Stub\Mocker;

use Mockery;

use PHPSpec2\Stub\MethodExpectationStub;
use PHPSpec2\Stub\ArgumentsResolver;

class MockeryMockProxy implements MockProxyInterface
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
        return new MethodExpectationStub(
            $this->originalMock->shouldReceive($method),
            $resolver,
            $arguments
        );
    }
}
