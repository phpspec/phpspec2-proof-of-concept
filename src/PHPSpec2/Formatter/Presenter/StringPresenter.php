<?php

namespace PHPSpec2\Formatter\Presenter;

class StringPresenter implements PresenterInterface
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

        switch ($type = strtolower(gettype($value))) {
            case 'null':
                return $this->presentString('[null]');
            case 'boolean':
                return $this->presentString(sprintf(
                    '[bool:%s]', true === $value ? 'true' : 'false'
                ));
            case 'object':
                return $this->presentString(sprintf('[%s]', get_class($value)));
            case 'array':
                return $this->presentString(sprintf('[array:%d]', count($value)));
            case 'string':
                if (30 > strlen($value) && false === strpos($value, "\n")) {
                    return $this->presentString(sprintf('[string:"%s"]', $value));
                }
                return $this->presentString('[string:...]');
            default:
                return $this->presentString(sprintf('[%s:%s]', $type, $value));
        }
    }

    public function presentString($string)
    {
        return $string;
    }
}
