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
        $events = array('beforeSpecification', 'afterExample', 'afterSuite');

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
            sprintf("\n----  %s\n", $event->getSpecification()->getTitle()),
            2 * $event->getSpecification()->getDepth()
        );
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
        $failedEvents = $this->stats->getFailedEvents();
        if (count($failedEvents)) {
            $this->io->writeln("\n<failed>====  failed examples</failed>\n");
        }
        foreach ($failedEvents as $event) {
            $example  = $event->getExample();
            $function = $example->getFunction();

            $this->io->writeln(sprintf('<lineno>%4d</lineno>  %s',
                $function->getStartLine(),
                str_replace(getcwd().DIRECTORY_SEPARATOR, '', $function->getFileName())
            ));
            $this->io->writeln(sprintf('<failed>✘ %s</failed>',
                $example->getTitle()
            ), 6);
            $this->printException($event, 8);
        }
        $brokenEvents = $this->stats->getBrokenEvents();
        if (count($brokenEvents)) {
            $this->io->writeln("\n<broken>====  broken examples</broken>\n");
        }
        foreach ($brokenEvents as $event) {
            $example  = $event->getExample();
            $function = $example->getFunction();

            $this->io->writeln(sprintf('<lineno>%4d</lineno>  %s',
                $function->getStartLine(),
                str_replace(getcwd().DIRECTORY_SEPARATOR, '', $function->getFileName())
            ));
            $this->io->writeln(sprintf('<broken>! %s</broken>',
                $example->getTitle()
            ), 6);
            $this->printException($event, 8);
        }

        $counts = array();
        foreach ($this->stats->getCountsHash() as $type => $count) {
            if ($count) {
                $counts[] = sprintf('<%s>%d %s</%s>', $type, $count, $type, $type);
            }
        }

        $this->io->write(sprintf("\n%d examples ", $this->stats->getEventsCount()));
        if (count($counts)) {
            $this->io->write(sprintf("(%s)", implode(', ', $counts)));
        }

        $this->io->writeln(sprintf(
            "\n%s", round($event->getTime() * 1000) . 'ms'
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

    protected function printException(ExampleEvent $event, $depth = null)
    {
        if (null === $exception = $event->getException()) {
            return;
        }

        // TODO: add cause to exception interface
        $exception->cause = $event->getExample()->getFunction();
        $depth = $depth ?: (($event->getExample()->getDepth() * 2) + 6);
        $message = $this->presenter->presentException($exception, $this->io->isVerbose());

        if (ExampleEvent::FAILED === $event->getResult()) {
            $this->io->writeln(sprintf('<failed>%s</failed>', lcfirst($message)), $depth);
        } else {
            $this->io->writeln(sprintf('<broken>%s</broken>', lcfirst($message)), $depth);
        }
    }
}
