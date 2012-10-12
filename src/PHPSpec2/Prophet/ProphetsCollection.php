<?php

namespace PHPSpec2\Prophet;

class ProphetsCollection
{
    private $subject;
    private $collaborators = array();

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject(ProphetInterface $subject)
    {
        $this->subject = $subject;
    }

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
        if ($this->hasCollaborator($name)) {
            return $this->collaborators[$name];
        }
    }
}
