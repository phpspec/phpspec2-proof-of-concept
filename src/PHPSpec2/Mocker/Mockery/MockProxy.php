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
        return new ExpectationProxy(
            $this->originalMock->shouldReceive($method),
            $arguments,
            $resolver
        );
    }
}
