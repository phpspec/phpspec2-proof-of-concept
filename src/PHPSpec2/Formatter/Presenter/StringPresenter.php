<?php

namespace PHPSpec2\Formatter\Presenter;

use Exception;
use PHPSpec2\Exception\Exception as PHPSpec2Exception;
use PHPSpec2\Exception\Example\NotEqualException;

class StringPresenter implements PresenterInterface
{
    private $differ;

    public function __construct(Differ\Differ $differ)
    {
        $this->differ = $differ;
    }

    public function presentValue($value)
    {
        if (is_callable($value)) {
            if (is_array($value)) {
                return $this->presentString(sprintf(
                    '[%s::%s()]', get_class($value[0]), $value[1]
                ));
            } elseif ($value instanceof \Closure) {
                return $this->presentString('[closure]');
            } else {
                return $this->presentString(sprintf('[%s()]', $value));
            }
        }

        if (is_object($value) && $value instanceof Exception) {
            return $this->presentString(sprintf(
                '[exc:%s("%s")]', get_class($value), $value->getMessage()
            ));
        }

        switch ($type = strtolower(gettype($value))) {
            case 'null':
                return $this->presentString('null');
            case 'boolean':
                return $this->presentString(sprintf('%s', true === $value ? 'true' : 'false'));
            case 'object':
                return $this->presentString(sprintf('[obj:%s]', get_class($value)));
            case 'array':
                return $this->presentString(sprintf('[array:%d]', count($value)));
            case 'string':
                if (25 > strlen($value) && false === strpos($value, "\n")) {
                    return $this->presentString(sprintf('"%s"', $value));
                }

                $lines = explode("\n", $value);
                return $this->presentString(sprintf('"%s"...', substr($lines[0], 0, 25)));
            default:
                return $this->presentString(sprintf('[%s:%s]', $type, $value));
        }
    }

    public function presentException(Exception $exception, $verbose = false)
    {
        $presentation = sprintf('Exception %s has been thrown.', $this->presentValue($exception));
        if ($exception instanceof PHPSpec2Exception) {
            $presentation = $exception->getMessage();
        }

        if (!$verbose) {
            return $presentation;
        }

        if ($exception instanceof NotEqualException) {
            if ($diff = $this->presentExceptionDifference($exception)) {
                return $presentation."\n".$diff;
            }
        }

        list($file, $line) = $this->getExceptionExamplePosition($exception);
        $presentation .= "\n".$this->presentFileCode($file, $line);

        if (!$exception instanceof PHPSpec2Exception) {
            if (trim($trace = $this->presentExceptionStackTrace($exception))) {
                return $presentation."\n".$trace;
            }
        }

        return $presentation;
    }

    public function presentString($string)
    {
        return $string;
    }

    public function presentCodeLine($number, $line)
    {
        return $number.' '.$line;
    }

    public function presentHighlight($line)
    {
        return $line;
    }

    protected function presentFileCode($file, $lineno, $context = 6)
    {
        $lines  = explode("\n", file_get_contents($file));
        $offset = max(0, $lineno - ceil($context / 2));
        $lines  = array_slice($lines, $offset, $context);

        $text = "\n";
        foreach ($lines as $line) {
            $offset++;

            if ($offset == $lineno) {
                $text .= $this->presentHighlight(sprintf('%4d', $offset).' '.$line);
            } else {
                $text .= $this->presentCodeLine(sprintf('%4d', $offset), $line);
            }

            $text .= "\n";
        }

        return $text;
    }

    protected function presentExceptionDifference(Exception $exception)
    {
        return $this->differ->compare($exception->getExpected(), $exception->getActual());
    }

    protected function presentExceptionStackTrace(Exception $exception)
    {
        list($file, $line) = $this->getExceptionExamplePosition($exception);

        $refl = $exception->cause;
        $text = "\n";
        foreach ($exception->getTrace() as $call) {
            if (isset($call['class'])
                && $refl->getDeclaringClass()->getName() == $call['class']
                && $refl->getName() == $call['function']) {
                break;
            }
            if (isset($call['file']) && $file == $call['file'] && $line == $call['line']) {
                break;
            }

            $excFile = isset($call['file']) ? $call['file'] : $exception->getFile();
            $excLine = isset($call['line']) ? $call['line'] : $exception->getLine();

            if (isset($call['class']) && isset($call['function'])) {
                $args = array_map(array($this, 'presentValue'), $call['args']);
                $text .= sprintf("<lineno>%4d</lineno> <trace-type>%s</trace-type>\n     %s%s%s(%s)\n",
                    $excLine, str_replace(getcwd().DIRECTORY_SEPARATOR, '', $excFile),
                    '<trace-class>'.$call['class'].'</trace-class>',
                    '<trace-type>'.$call['type'].'</trace-type>',
                    '<trace-func>'.$call['function'].'</trace-func>',
                    '<trace-args>'.implode(', ', $args).'</trace-args>'
                );
            } elseif (isset($call['function'])) {
                $args = array_map(array($this, 'presentValue'), $call['args']);

                $text .= sprintf("<lineno>%4d</lineno> <trace-type>%s</trace-type>\n     %s(%s)\n",
                    $excLine, str_replace(getcwd().DIRECTORY_SEPARATOR, '', $excFile),
                    '<trace-func>'.$call['function'].'</trace-func>',
                    '<trace-args>'.implode(', ', $args).'</trace-args>'
                );
            }
        }

        return $text;
    }

    protected function getExceptionExamplePosition(Exception $exception)
    {
        $refl = $exception->cause;
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
}
