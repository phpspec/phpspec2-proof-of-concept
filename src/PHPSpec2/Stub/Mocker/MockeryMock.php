<?php

namespace PHPSpec2\Stub\Mocker;

use Mockery;

use PHPSpec2\Stub\MethodExpectationStub;
use PHPSpec2\Stub\ArgumentsResolver;

class MockeryMock implements MockInterface
{
    private $subject;

    public function __construct($classOrInterface)
    {
        $this->subject = Mockery::mock($classOrInterface);
        $this->subject->shouldIgnoreMissing();
    }

    public function mockMethod($method, array $arguments, ArgumentsResolver $resolver)
    {
        return new MethodExpectationStub(
            $this->subject->shouldReceive($method),
            $resolver,
            $arguments
        );
    }
}
