<?php

namespace PHPSpec2\Stub\Mocker;

use PHPSpec2\Stub\ArgumentsResolver;

interface MockProxyInterface
{
    public function getOriginalMock();
    public function mockMethod($method, array $arguments, ArgumentsResolver $resolver);
}
