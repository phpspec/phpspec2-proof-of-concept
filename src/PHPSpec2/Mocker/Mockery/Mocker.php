<?php

namespace PHPSpec2\Mocker\Mockery;

use PHPSpec2\Mocker\MockerInterface;

use Mockery;

class Mocker implements MockerInterface
{
    public function mock($classOrInterface)
    {
        return new MockProxy($classOrInterface);
    }

    public function teardown()
    {
        Mockery::close();
    }
}
