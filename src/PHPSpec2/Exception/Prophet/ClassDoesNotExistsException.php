<?php

namespace PHPSpec2\Exception\Prophet;

class ClassDoesNotExistsException extends ProphetException
{
    private $classname;

    public function __construct($classname)
    {
        $this->classname = $classname;

        parent::__construct(sprintf(
            'Class <value>%s</value> does not exists.',
            $classname
        ));
    }

    public function getClassname()
    {
        return $this->classname;
    }
}
