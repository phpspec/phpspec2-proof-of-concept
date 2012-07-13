<?php

namespace PHPSpec2\Formatter;

use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Console\IO;

use ReflectionMethod;
use Exception;

class PrettyFormatter implements FormatterInterface
{
    private $io;

    public static function getSubscribedEvents()
    {
        $events = array('beforeSpecification', 'afterExample');

        return array_combine($events, $events);
    }

    public function setIO(IO $io)
    {
        $this->io = $io;
    }

    public function beforeSpecification(SpecificationEvent $event)
    {
        $this->writeln(sprintf(
            "\n%s", $event->getSpecification()->getName()
        ));
    }

    public function afterExample(ExampleEvent $event)
    {
        switch ($event->getResult()) {
            case ExampleEvent::PASSED:
                $this->writeln(sprintf(
                    "<passed>✔ %s</passed>",
                    $this->formatExampleName($event->getExample())
                ));
                break;
            case ExampleEvent::PENDING:
                $this->writeln(sprintf(
                    "<pending>- %s</pending>",
                    $this->formatExampleName($event->getExample())
                ));
                $this->writeln(sprintf(
                    "<pending>  %s</pending>",
                    $this->formatExampleException($event->getException(), false)
                ));
                break;
            case ExampleEvent::FAILED:
                $this->writeln(sprintf(
                    "<failed>✘ %s</failed>",
                    $this->formatExampleName($event->getExample())
                ));
                $this->writeln(sprintf(
                    "<failed>  %s</failed>",
                    $this->formatExampleException($event->getException())
                ));
                break;
        }
    }

    protected function formatExampleName(ReflectionMethod $example)
    {
        return str_replace('_', ' ', $example->getName());
    }

    protected function formatExampleException(Exception $exception, $verbose = false)
    {
        return get_class($exception).': '.$exception->getMessage();
    }

    private function writeln($text)
    {
        $this->io->getOutput()->writeln($text);
    }
}
