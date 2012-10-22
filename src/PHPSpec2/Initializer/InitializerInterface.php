<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\SpecificationInterface;
use PHPSpec2\Loader\Node\Example;
use PHPSpec2\Prophet\CollaboratorsCollection;
use PHPSpec2\Matcher\MatchersCollection;

interface InitializerInterface
{
    public function getPriority();
    public function supports(SpecificationInterface $specification, Example $example);
    public function initialize(SpecificationInterface $specification, Example $example,
                               CollaboratorsCollection $prophets, MatchersCollection $matchers);
}
