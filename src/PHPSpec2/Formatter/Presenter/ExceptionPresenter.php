<?php

namespace PHPSpec2\Formatter\Presenter;


use PHPSpec2\Exception\Exception;
use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Exception\Example\MockerException;
use PHPSpec2\Exception\Example\ErrorException;
use PHPSpec2\Exception\Example\PendingException;


class ExceptionPresenter implements ExceptionPresenterInterface
{
    private $valuePresenter;
    private $differ;

    public function __construct(PresenterInterface $valuePresenter, Differ\Differ $differ)
    {
        $this->valuePresenter = $valuePresenter;
        $this->differ = $differ;
    }

    public function presentException(Exception $exception, $verbose = false)
    {
        $presentation = sprintf('Exception %s has been thrown.', $this->getValuePresenter()->presentValue($exception));

        if ($exception instanceof Exception) {
            $presentation = wordwrap($exception->getMessage(), 120);
        }

        if (!$verbose || $exception instanceof PendingException) {

            return $presentation;
        }

        if ($exception instanceof NotEqualException) {
            if ($diff = $this->presentExceptionDifference($exception)) {
                return $presentation . "\n" . $diff;
            }
        }

        if ($exception instanceof MockerException) {
            return $exception->getMessage();
        }

        if ($exception instanceof Exception) {
            list($file, $line) = $this->getExceptionExamplePosition($exception);

            return $presentation . "\n" . $this->getValuePresenter()->presentFileCode($file, $line);
        }

        if (trim($trace = $this->presentExceptionStackTrace($exception))) {
            return $presentation . "\n" . $trace;
        }
        return $presentation;
    }


    public function presentExceptionDifference(Exception $exception)
    {
        return $this->getDiffer()->compare($exception->getExpected(), $exception->getActual());
    }

    public function presentExceptionStackTrace(Exception $exception)
    {
        $phpspecPath = dirname(dirname(__DIR__));
        $runnerPath = $phpspecPath . DIRECTORY_SEPARATOR . 'Runner';

        $offset = 0;
        $text = "\n";

        $text .= $this->presentExceptionTraceHeader(sprintf("%2d %s:%d",
            $offset++,
            str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $exception->getFile()),
            $exception->getLine()
        ));
        $text .= $this->presentExceptionTraceFunction(
            'throw new ' . get_class($exception), array($exception->getMessage())
        );

        foreach ($exception->getTrace() as $call) {
            // skip internal framework calls
            if (isset($call['file']) && false !== strpos($call['file'], $runnerPath)) {
                break;
            }
            if (isset($call['file']) && 0 === strpos($call['file'], $phpspecPath)) {
                continue;
            }
            if (isset($call['class']) && 0 === strpos($call['class'], "PHPSpec2\\")) {
                continue;
            }

            if (isset($call['file'])) {
                $text .= $this->presentExceptionTraceHeader(sprintf("%2d %s:%d",
                    $offset++,
                    str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $call['file']),
                    $call['line']
                ));
            } else {
                $text .= $this->presentExceptionTraceHeader(sprintf("%2d [internal]", $offset++));
            }

            if (isset($call['class'])) {
                $text .= $this->presentExceptionTraceMethod(
                    $call['class'], $call['type'], $call['function'], $call['args']
                );
            } elseif (isset($call['function'])) {
                $args = array_map(array($this, 'presentValue'), $call['args']);

                $text .= $this->presentExceptionTraceFunction(
                    $call['function'], $call['args']
                );
            }
        }

        return $text;
    }

    protected function presentExceptionTraceHeader($header)
    {
        return $header . "\n";
    }

    public function presentExceptionTraceMethod($class, $type, $method, array $args)
    {
        $args = array_map(array($this, 'presentValue'), $args);

        return sprintf("   %s%s%s(%s)\n", $class, $type, $method, implode(', ', $args));
    }

    protected function presentValue($value)
    {
        return $this->getValuePresenter()->presentValue($value);
    }

    public function presentExceptionTraceFunction($function, array $args)
    {
        $args = array_map(array($this, 'presentValue'), $args);

        return sprintf("   %s(%s)\n", $function, implode(', ', $args));
    }

    protected function getExceptionExamplePosition(Exception $exception)
    {
        $refl = $exception->getCode();
        foreach ($exception->getTrace() as $call) {
            if (!isset($call['file'])) {
                continue;
            }

            if ($refl->getFilename() === $call['file']) {
                return array($call['file'], $call['line']);
            }
        }

        return array($exception->getFile(), $exception->getLine());
    }

    public function setDiffer($differ)
    {
        $this->differ = $differ;
    }

    public function getDiffer()
    {
        return $this->differ;
    }

    public function setValuePresenter($valuePresenter)
    {
        $this->valuePresenter = $valuePresenter;
    }

    public function getValuePresenter()
    {
        return $this->valuePresenter;
    }

}
