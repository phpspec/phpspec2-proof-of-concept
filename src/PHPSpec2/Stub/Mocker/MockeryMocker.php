<?php

namespace PHPSpec2\Stub\Mocker;

class MockeryMocker implements MockerInterface
{
    public function mock($classOrInterface)
    {
        return new MockeryMock($classOrInterface);
    }
}
