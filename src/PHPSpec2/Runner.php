<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Runner
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->eventDispatcher = $dispatcher;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}
