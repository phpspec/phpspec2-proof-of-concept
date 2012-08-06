<?php

namespace PHPSpec2\Stub;

class MockerFactory
{
    private $mocker;

    public function __construct(Mocker\MockerInterface $mocker = null)
    {
        $this->mocker = $mocker ?: new Mocker\MockeryMocker;
    }

    public function mock($classOrInterface)
    {
        return $this->mocker->mock($classOrInterface);
    }
}
