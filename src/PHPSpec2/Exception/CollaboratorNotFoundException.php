<?php

namespace PHPSpec2\Exception;

class CollaboratorNotFoundException extends Exception
{
    private $name;

    public function __construct($message, $name)
    {
        parent::__construct($message);

        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
