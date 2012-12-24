<?php

namespace PHPSpec2\Formatter\Presenter;

use Exception;
use PHPSpec2\Exception\Exception as PHPSpec2Exception;
use PHPSpec2\Exception\Example\NotEqualException;
use PHPSpec2\Exception\Example\MockerException;
use PHPSpec2\Exception\Example\ErrorException;
use PHPSpec2\Exception\Example\PendingException;

class ValuePresenter implements PresenterInterface
{

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

    public function presentFileCode($file, $lineno, $context = 6)
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

    public function presentString($string)
    {
        return $string;
    }

    protected function presentCodeLine($number, $line)
    {
        return $number.' '.$line;
    }

    protected function presentHighlight($line)
    {
        return $line;
    }

}
