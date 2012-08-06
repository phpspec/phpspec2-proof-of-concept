<?php

namespace PHPSpec2\Mocker;

use PHPSpec2\Stub\ArgumentsResolver;

interface MockProxyInterface
{
    public function getOriginalMock();
    public function mockMethod($method, array $arguments, ArgumentsResolver $resolver);
}
