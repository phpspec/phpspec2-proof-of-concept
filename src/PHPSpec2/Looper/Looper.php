<?php

namespace src\PHPSpec2\Looper\Looper.php;

class Looper
{
    private $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function __call($method, array $arguments)
    {
        return call_user_func($this->callable, $method, $arguments);
    }
}
