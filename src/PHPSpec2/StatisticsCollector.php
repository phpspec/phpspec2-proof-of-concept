<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use PHPSpec2\Event\ExampleEvent;

class StatisticsCollector implements EventSubscriberInterface
{
    private $startTime;
    private $finishTime;
    private $globalResult  = 0;
    private $passedEvents  = array();
    private $pendingEvents = array();
    private $failedEvents  = array();

    public static function getSubscribedEvents()
    {
        return array(
            'beforeSuite'  => array('beforeSuite', 10),
            'afterSuite'   => array('afterSuite', 10),
            'afterExample' => array('afterExample', 10),
        );
    }

    public function beforeSuite()
    {
        $this->startTime = microtime(true);
    }

    public function afterSuite()
    {
        $this->finishTime = microtime(true);
    }

    public function afterExample(ExampleEvent $event)
    {
        $this->globalResult = max($this->globalResult, $event->getResult());

        switch ($event->getResult()) {
            case ExampleEvent::PASSED:
                $this->passedEvents[] = $event;
                break;
            case ExampleEvent::PENDING:
                $this->pendingEvents[] = $event;
                break;
            case ExampleEvent::FAILED:
                $this->failedEvents[] = $event;
                break;
        }
    }

    public function getTotalTime()
    {
        return $this->finishTime - $this->startTime;
    }

    public function getGlobalResult()
    {
        return $this->globalResult;
    }

    public function getAllEvents()
    {
        return array_merge(
            $this->passedEvents,
            $this->pendingEvents,
            $this->failedEvents
        );
    }

    public function getPassedEvents()
    {
        return $this->passedEvents;
    }

    public function getPendingEvents()
    {
        return $this->pendingEvents;
    }

    public function getFailedEvents()
    {
        return $this->failedEvents;
    }
}
