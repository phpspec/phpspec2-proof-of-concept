<?php

namespace PHPSpec2\Exception;

class CollaboratorNotFoundException extends Exception
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;

        parent::__construct(sprintf('Collaborator <value>%s</value> not found.', $name));
    }

    public function getName()
    {
        return $this->name;
    }
}
