<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Exception\CollaboratorNotFoundException;

class ProphetsCollection
{
    private $collaborators = array();

    public function getCollaborators()
    {
        return $this->collaborators;
    }

    public function setCollaborator($name, ProphetInterface $collaborator)
    {
        $this->collaborators[$name] = $collaborator;
    }

    public function hasCollaborator($name)
    {
        return isset($this->collaborators[$name]);
    }

    public function getCollaborator($name)
    {
        if (!$this->hasCollaborator($name)) {
            throw new CollaboratorNotFoundException($name);
        }

        return $this->collaborators[$name];
    }
}
