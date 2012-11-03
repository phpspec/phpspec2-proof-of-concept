<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\Exception\CollaboratorNotFoundException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class CollaboratorsCollection
{
    private $presenter;
    private $collaborators = array();

    public function __construct(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }

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
            throw new CollaboratorNotFoundException(
                sprintf('Collaborator %s not found.', $this->presenter->presentString($name)),
                $name
            );
        }

        return $this->collaborators[$name];
    }
}
