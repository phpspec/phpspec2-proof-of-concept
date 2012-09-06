<?php

namespace PHPSpec2\Formatter;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use PHPSpec2\Console\IO;
use PHPSpec2\Formatter\Representer\RepresenterInterface;
use PHPSpec2\Event\SuiteEvent;
use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Formatter\Diff\StringDiff;
use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\ExampleException;
use PHPSpec2\Exception\Example\NotEqualException;

use ReflectionClass;
use ReflectionMethod;
use Exception;

class PrettyFormatter implements FormatterInterface
{
    private $io;
    private $representer;

    public function __construct(RepresenterInterface $representer)
    {
        $this->representer = $representer;
    }

    public static function getSubscribedEvents()
    {
        $events = array('beforeSpecification', 'afterExample', 'afterSuite');

        return array_combine($events, $events);
    }

    public function setIO(IO $io)
    {
        $this->io = $io;
    }

    public function beforeSpecification(SpecificationEvent $event)
    {
        $this->writeln(sprintf(
            "\n> %s\n", $this->formatSpecificationName($event->getSpecification())
        ));
    }

    public function afterExample(ExampleEvent $event)
    {
        switch ($event->getResult()) {
            case ExampleEvent::PASSED:
                $this->writeln(sprintf(
                    "  <passed>✔ %s</passed>",
                    $this->formatExampleName($event->getExample())
                ));
                break;
            case ExampleEvent::PENDING:
                $this->writeln(sprintf(
                    "  <pending>- %s</pending>",
                    $this->formatExampleName($event->getExample())
                ));
                $this->writeln(sprintf(
                    "<pending>%s</pending>\n",
                    $this->formatExampleException($event->getException(), false)
                ));
                break;
            case ExampleEvent::FAILED:
                $this->writeln(sprintf(
                    "  <failed>✘ %s</failed>",
                    $this->formatExampleName($event->getExample())
                ));
                $this->writeln(sprintf(
                    "<failed>%s</failed>\n",
                    $this->formatExampleException($event->getException(), $this->isVerbose())
                ));
                break;
        }
    }

    public function afterSuite(SuiteEvent $event)
    {
        $stats = $event->getStatisticsCollector();

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

        $this->writeln(sprintf(
            "\n%d examples (%s)", count($stats->getAllEvents()), implode(', ', $counts)
        ));

        $time    = $stats->getTotalTime();
        $minutes = floor($time / 60);
        $seconds = round($time - ($minutes * 60), 3);

        $this->writeln($minutes . 'm' . $seconds . 's');
    }

    protected function formatSpecificationName(ReflectionClass $specification)
    {
        return str_replace('Spec\\', '', $specification->getName());
    }

    protected function formatExampleName(ReflectionMethod $example)
    {
        return str_replace('_', ' ', $example->getName());
    }

    protected function formatExampleException(Exception $exception, $verbose = false)
    {
        if (!$verbose) {
            return $this->padText($this->getExceptionMessage($exception), 4);
        } else {
            return $this->padText($this->getExceptionMessage($exception, false), 4) . "\n\n" .
                $this->padText($this->getExceptionStackTrace($exception), 4);
        }
    }

    private function getExceptionMessage(Exception $exception, $lineno = true)
    {
        if (!$exception instanceof ExampleException) {
            $message = sprintf(
                'Exception <value>%s("%s")</value> has been thrown.',
                get_class($exception),
                $exception->getMessage()
            );
        } else {
            $message = $exception->getMessage();
        }

        if ($lineno) {
            list($file, $line) = $this->getExceptionInitialPosition($exception);
            $message = sprintf('<lineno>%4d</lineno> %s', $line, $message);
        }

        return $message;
    }

    private function getExceptionStackTrace(Exception $exception)
    {
        if ($exception instanceof NotEqualException) {
            return rtrim(StringDiff::diff($exception->getExpected(), $exception->getActual()));
        }

        if ($exception instanceof MatcherException) {
            list($file, $lineno) = $this->getExceptionInitialPosition($exception);

            $showLines = 6;
            $lines  = file_get_contents($file);
            $lines  = explode("\n", $lines);
            $offset = max(0, $lineno - ceil($showLines / 2));

            $lines = array_slice($lines, $offset, $showLines);

            $text = '';
            foreach ($lines as $line) {
                $offset++;

                if ($offset == $lineno) {
                    $text .= sprintf("<hl>%4d</hl> <hl>%s</hl>\n", $offset, $line);
                } else {
                    $text .= sprintf("<lineno>%4d</lineno> <code>%s</code>\n", $offset, $line);
                }
            }

            return rtrim($text);
        }

        $text = '';
        $offset = 0;
        $representer = $this->representer;
        foreach ($trace = $exception->getTrace() as $call) {
            if (isset($call['class']) && isset($call['function'])) {

                $args = array_map(function($item) use($representer) {
                    return $representer->representValue($item);
                }, $call['args']);
                $text .= sprintf("<lineno>%4d</lineno> %s%s%s(%s)\n",
                    $offset++,
                    '<trace-class>'.$call['class'].'</trace-class>',
                    '<trace-type>'.$call['type'].'</trace-type>',
                    '<trace-func>'.$call['function'].'</trace-func>',
                    '<trace-args>'.implode(', ', $args).'</trace-args>'
                );

                if (0 === strpos($call['function'], 'it_')) {
                    break;
                }
                if (0 === strpos($call['function'], 'it_')) {
                    break;
                }
            } elseif (isset($call['function'])) {
                $args = array_map(function($item) use($representer) {
                    return $representer->representValue($item);
                }, $call['args']);
                $text .= sprintf("<lineno>%4d</lineno> %s(%s)\n",
                    $offset++,
                    '<trace-func>'.$call['function'].'</trace-func>',
                    '<trace-args>'.implode(', ', $args).'</trace-args>'
                );
            }
        }

        return $text;
    }

    private function getExceptionInitialPosition(Exception $exception)
    {
        $trace = $exception->getTrace();
        foreach ($trace as $i => $call) {
            if (!isset($trace[$i + 1])) {
                continue;
            }

            $next = $trace[$i + 1];
            if (0 === strpos($next['function'], 'it')) {
                return array($call['file'], $call['line']);
            }
        }

        return array($exception->getFile(), $exception->getLine());
    }

    private function padText($text, $indent = 2)
    {
        return implode("\n", array_map(function($line) use($indent) {
            return str_repeat(' ', $indent).$line;
        }, explode("\n", $text)));
    }

    private function writeln($text)
    {
        $this->io->getOutput()->writeln($text);
    }

    private function isVerbose()
    {
        return $this->io->getOutput()->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE;
    }
}
