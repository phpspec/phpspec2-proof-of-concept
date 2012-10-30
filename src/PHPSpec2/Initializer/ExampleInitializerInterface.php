<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\SpecificationInterface;
use PHPSpec2\Loader\Node\Example;
use PHPSpec2\Prophet\CollaboratorsCollection;

interface ExampleInitializerInterface
{
    public function getPriority();
    public function supports(SpecificationInterface $specification, Example $example);
    public function initialize(SpecificationInterface $specification, Example $example,
                               CollaboratorsCollection $collaborators);
}
