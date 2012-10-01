<?php

namespace PHPSpec2\Formatter;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use PHPSpec2\Console\IO;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Event\SuiteEvent;
use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;
use PHPSpec2\Formatter\Diff\Diff;
use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Exception\Exception as PHPSpec2Exception;

use Mockery\CountValidator\Exception as MockeryCountException;
use Mockery\Exception as MockeryException;

use ReflectionClass;
use ReflectionMethod;
use Exception;
use PHPSpec2\Loader\Node\Example;

class PrettyFormatter implements FormatterInterface
{
    private $io;
    private $presenter;
    private $differ;

    public function __construct(PresenterInterface $presenter, Diff $differ)
    {
        $this->presenter = $presenter;
        $this->differ      = $differ;
    }

    public static function getSubscribedEvents()
    {
        $events = array(
            'beforeSpecification', 'afterExample', 'afterSuite'
        );

        return array_combine($events, $events);
    }

    public function setIO(IO $io)
    {
        $this->io = $io;
    }

    public function beforeSpecification(SpecificationEvent $event)
    {
        $this->io->writeln($this->padText(
            sprintf("\n%s%s\n",
                $event->getSpecification()->getParent() ? '::' : '> ',
                $event->getSpecification()->getTitle()
            ),
            2 * $event->getSpecification()->getDepth()
        ));
    }

    public function afterExample(ExampleEvent $event)
    {
        switch ($event->getResult()) {
            case ExampleEvent::PASSED:
                $this->io->write(sprintf(
                    $this->padText('<passed>✔ %s</passed>', 2 * $event->getExample()->getDepth()),
                    $event->getExample()->getTitle()
                ));

                $ms = $event->getTime() * 1000;
                if ($ms > 100) {
                    $this->io->write(sprintf(' <failed>(%sms)</failed>', round($ms)));
                } elseif ($ms > 50) {
                    $this->io->write(sprintf(' <pending>(%sms)</pending>', round($ms)));
                }
                $this->io->writeln('');

                break;
            case ExampleEvent::PENDING:
                $this->io->writeln(sprintf(
                    $this->padText('<pending>- %s</pending>', 2 * $event->getExample()->getDepth()),
                    $event->getExample()->getTitle()
                ));
                $this->io->writeln(sprintf(
                    "<pending>%s</pending>\n",
                    $this->formatExampleException(
                        $event->getExample(), $event->getException(), false
                    )
                ));
                break;
            case ExampleEvent::FAILED:
                $this->io->writeln(sprintf(
                    $this->padText('<failed>✘ %s</failed>', 2 * $event->getExample()->getDepth()),
                    $event->getExample()->getTitle()
                ));
                $this->io->writeln(sprintf(
                    "<failed>%s</failed>\n",
                    $this->formatExampleException(
                        $event->getExample(), $event->getException(), $this->io->isVerbose()
                    )
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

    protected function formatExampleException(Example $example, Exception $exception, $verbose = false)
    {
        $message = $this->getExceptionMessage($exception);
        $trace   = $this->getExceptionStackTrace($exception);

        if (!$verbose || null === $trace) {
            return $this->padText($message, 2 * ($example->getDepth() + 1));
        } else {
            return $this->padText($message, 2 * ($example->getDepth() + 1)) . "\n\n" .
                $this->padText($trace, 2 * ($example->getDepth() + 1));
        }
    }

    private function getExceptionMessage(Exception $exception, $lineno = true)
    {
        $message = sprintf(
            'Exception %s has been thrown.', $this->presenter->presentValue($exception)
        );

        if ($exception instanceof PHPSpec2Exception) {
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
        if ($exception instanceof MockeryCountException) {
            return 'Check your mocks expectations.';
        }

        if ($exception instanceof NotEqualException) {
            return $this->differ->compare(
                $exception->getExpected(), $exception->getActual()
            );
        }

        if ($exception instanceof PHPSpec2Exception) {
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
        $presenter = $this->presenter;
        foreach ($trace = $exception->getTrace() as $call) {
            if (isset($call['class']) && isset($call['function'])) {

                $args = array_map(function($item) use($presenter) {
                    return $presenter->presentValue($item);
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
                $args = array_map(function($item) use($presenter) {
                    return $presenter->presentValue($item);
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
}
