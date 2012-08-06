<?php

namespace PHPSpec2\Mocker\Mockery;

use PHPSpec2\Mocker\MockerInterface;

class Mocker implements MockerInterface
{
    public function mock($classOrInterface)
    {
        return new MockProxy($classOrInterface);
    }
}
