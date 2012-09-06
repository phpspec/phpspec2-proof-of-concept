<?php

namespace PHPSpec2\Formatter\Representer;

class BasicRepresenter implements RepresenterInterface
{
    public function representValue($value)
    {
        if (is_callable($value)) {
            if (is_array($value)) {
                return sprintf('<method:%s::%s()>', $value[0], $value[1]);
            } else {
                return sprintf('<function:%s()>', $value);
            }
        }

        switch ($type = strtolower(gettype($value))) {
            case 'null':
                return '<null>';
            case 'boolean':
                return true === $value ? '<bool:true>' : '<bool:false>';
            case 'object':
                return sprintf('<object:%s>', get_class($value));
            case 'array':
                return sprintf('<array(%d)>', count($value));
            case 'string':
                if (30 > strlen($value)) {
                    return sprintf('<string:"%s">', $value);
                }
                return '<string>';
            default:
                return sprintf('<%s:%s>', $type, $value);
        }
    }
}
