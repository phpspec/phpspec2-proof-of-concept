<?php

namespace PHPSpec2\Mocker;

class MockerFactory
{
    private $mocker;

    public function __construct(MockerInterface $mocker = null)
    {
        $this->mocker = $mocker ?: new Mockery\Mocker;
    }

    public function setMocker(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mock($classOrInterface)
    {
        $mock = $this->mocker->mock($classOrInterface);

        return $mock;
    }
}
