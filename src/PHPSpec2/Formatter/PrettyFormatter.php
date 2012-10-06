<?php

namespace PHPSpec2\Formatter;

use PHPSpec2\Console\IO;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Listener\StatisticsCollector;

use PHPSpec2\Event\SuiteEvent;
use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;

class PrettyFormatter implements FormatterInterface
{
    private $io;
    private $presenter;
    private $stats;

    public static function getSubscribedEvents()
    {
        $events = array(
            'beforeSpecification', 'afterSpecification', 'afterExample', 'afterSuite'
        );

        return array_combine($events, $events);
    }

    public function setIO(IO $io)
    {
        $this->io = $io;
    }

    public function setPresenter(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }

    public function setStatisticsCollector(StatisticsCollector $stats)
    {
        $this->stats = $stats;
    }

    public function beforeSpecification(SpecificationEvent $event)
    {
        $this->io->writeln(
            sprintf("\n      %s\n", $event->getSpecification()->getTitle()),
            2 * $event->getSpecification()->getDepth()
        );
    }

    public function afterSpecification(SpecificationEvent $event)
    {
        $this->io->writeln();
    }

    public function afterExample(ExampleEvent $event)
    {
        $line  = $event->getExample()->getFunction()->getStartLine();
        $depth = $event->getExample()->getDepth() * 2;
        $title = $event->getExample()->getTitle();

        $this->io->write(sprintf('<lineno>%4d</lineno> ', $line));

        switch ($event->getResult()) {
            case ExampleEvent::PASSED:
                $this->io->write(sprintf('<passed>✔ %s</passed>', $title), $depth - 1);
                break;
            case ExampleEvent::PENDING:
                $this->io->write(sprintf('<pending>- %s</pending>', $title), $depth - 1);
                break;
            case ExampleEvent::FAILED:
                $this->io->write(sprintf('<failed>✘ %s</failed>', $title), $depth - 1);
                break;
            case ExampleEvent::BROKEN:
                $this->io->write(sprintf('<broken>! %s</broken>', $title), $depth - 1);
                break;
        }

        $this->printSlowTime($event);
        $this->io->writeln();
        $this->printException($event);
    }

    public function afterSuite(SuiteEvent $event)
    {
        $stats = $this->stats;

        $counts = array();
        if ($count = count($stats->getPassedEvents())) {
            $counts[] = sprintf('<passed>%d passed</passed>', $count);
        }
        if ($count = count($stats->getPendingEvents())) {
            $counts[] = sprintf('<pending>%d pending</pending>', $count);
        }
        if ($count = count($stats->getFailedEvents())) {
            $counts[] = sprintf('<failed>%d failed</failed>', $count);
        }
        if ($count = count($stats->getBrokenEvents())) {
            $counts[] = sprintf('<broken>%d broken</broken>', $count);
        }

        $this->io->write(sprintf(
            "\n%d examples ", count($stats->getAllEvents())
        ));
        if (count($counts)) {
            $this->io->write(sprintf(
                "(%s)", implode(', ', $counts)
            ));
        }

        $this->io->writeln(sprintf(
            "\n%s", round($stats->getTotalTime() * 1000) . 'ms'
        ));
    }

    protected function printSlowTime(ExampleEvent $event)
    {
        $ms = $event->getTime() * 1000;
        if ($ms > 100) {
            $this->io->write(sprintf(' <failed>(%sms)</failed>', round($ms)));
        } elseif ($ms > 50) {
            $this->io->write(sprintf(' <pending>(%sms)</pending>', round($ms)));
        }
    }

    protected function printException(ExampleEvent $event)
    {
        if (null === $exception = $event->getException()) {
            return;
        }

        // TODO: add cause to exception interface
        $exception->cause = $event->getExample()->getFunction();
        $depth = ($event->getExample()->getDepth() * 2) + 6;
        $message = $this->presenter->presentException($exception, $this->io->isVerbose());

        if (ExampleEvent::FAILED === $event->getResult()) {
            $this->io->writeln(sprintf('<failed>%s</failed>', lcfirst($message)), $depth);
        } else {
            $this->io->writeln(sprintf('<broken>%s</broken>', lcfirst($message)), $depth);
        }
    }
}
