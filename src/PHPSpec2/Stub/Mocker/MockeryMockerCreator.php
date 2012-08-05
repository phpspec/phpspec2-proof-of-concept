<?php

namespace PHPSpec2\Stub\Mocker;

class MockeryMockerCreator implements MockerCreatorInterface
{
    public function createNew($classOrInstance)
    {
        return new MockeryMocker($classOrInstance);
    }
}
