<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Exception\CollaboratorNotFoundException;

class CollaboratorsCollection
{
    private $collaborators = array();

    public function getAll()
    {
        return $this->collaborators;
    }

    public function set($name, ProphetInterface $collaborator)
    {
        $this->collaborators[$name] = $collaborator;
    }

    public function has($name)
    {
        return isset($this->collaborators[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new CollaboratorNotFoundException($name);
        }

        return $this->collaborators[$name];
    }
}
