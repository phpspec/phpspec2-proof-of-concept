<?php

namespace PHPSpec2\Exception;

class ClassNotFoundException extends Exception
{
    private $classname;

    public function __construct($classname)
    {
        $this->classname = $classname;

        parent::__construct(sprintf(
            'Class <value>%s</value> does not exist.',
            $classname
        ));
    }

    public function getClassname()
    {
        return $this->classname;
    }
}
