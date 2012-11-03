<?php

namespace PHPSpec2\Exception;

class ClassNotFoundException extends Exception
{
    private $classname;

    public function __construct($message, $classname)
    {
        parent::__construct($message);

        $this->classname = $classname;
    }

    public function getClassname()
    {
        return $this->classname;
    }
}
