<?php

namespace PHPSpec2\Stub;

class MockerFactory
{
    private $mocker;

    public function __construct(Mocker\MockerInterface $mocker = null)
    {
        $this->mocker = $mocker ?: new Mocker\MockeryMocker;
    }

    public function setMocker(Mocker\MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mock($classOrInterface)
    {
        return $this->mocker->mock($classOrInterface);
    }
}
