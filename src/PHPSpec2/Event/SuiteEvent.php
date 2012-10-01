<?php

namespace PHPSpec2\Event;

use Symfony\Component\EventDispatcher\Event;

use PHPSpec2\Listener\StatisticsCollector;

class SuiteEvent extends Event implements EventInterface
{
    private $collector;

    public function __construct(StatisticsCollector $collector)
    {
        $this->collector = $collector;
    }

    public function getStatisticsCollector()
    {
        return $this->collector;
    }
}
