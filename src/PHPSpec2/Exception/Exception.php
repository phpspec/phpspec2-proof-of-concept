<?php

namespace PHPSpec2\Exception;

class Exception extends \Exception implements ExceptionInterface
{
    private $cause;


    public function setCause($cause)
    {
        $this->cause = $cause;
    }

    public function getCause()
    {
        return $this->cause;
    }
}
