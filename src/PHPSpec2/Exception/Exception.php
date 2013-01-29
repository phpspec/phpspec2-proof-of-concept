<?php

namespace PHPSpec2\Exception;

class Exception extends \Exception
{
    protected $cause;

    public function getCause()
    {
        return $this->cause;
    }

    public function setCause($cause)
    {
        $this->cause = $cause;
    }
}
