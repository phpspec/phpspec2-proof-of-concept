<?php

namespace PHPSpec2\Stub\Mocker;

use PHPSpec2\Stub\ArgumentsResolver;

interface MockInterface
{
    public function mockMethod($method, array $arguments, ArgumentsResolver $resolver);
}
