<?php

namespace PHPSpec2\Mocker;

define('ANY_ARGUMENTS', '__phpspec2_any_args__');

interface MockerInterface
{
    public function mock($classOrInterface);

    public function createExpectation($mock, $method, array $arguments = null);
    public function hasExpectation($mock, $method, array $arguments = null, $offset = null);
    public function getExpectation($mock, $method, array $arguments = null, $offset = null);

    public function makeDefault($expectation);
    public function shouldBeCalled($expectation);
    public function shouldNotBeCalled($expectation);

    public function withArguments($expectation, array $arguments = null);
    public function willReturn($expectation, $return);
    public function willReturnUsing($expectation, $callback);
    public function willThrow($expectation, $exception, $message = '');

    public function verify();
}
