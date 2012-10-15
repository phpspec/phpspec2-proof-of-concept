<?php

namespace PHPSpec2\Exception;

class FunctionNotFoundException extends Exception
{
    private $function;

    public function __construct($function)
    {
        $this->function = $function;

        parent::__construct(sprintf(
            'Function <value>%s()</value> not found.',
            $function
        ));
    }

    public function getFunction() {
        return $this->function;
    }

}
