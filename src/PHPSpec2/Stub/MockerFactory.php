<?php

namespace PHPSpec2\Stub;

class MockerFactory
{
    private $creator;

    public function __construct(Mocker\MockerCreatorInterface $creator = null)
    {
        $this->creator = $creator ?: new Mocker\MockeryMockerCreator;
    }

    public function createFor($classOrInstance)
    {
        return $this->creator->createNew($classOrInstance);
    }
}

