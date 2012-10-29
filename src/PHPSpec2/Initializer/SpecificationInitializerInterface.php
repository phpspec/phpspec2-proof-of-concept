<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\Loader\Node\Specification;
use PHPSpec2\Matcher\MatchersCollection;

interface SpecificationInitializerInterface
{
    public function getPriority();
    public function supports(Specification $specification);
    public function initialize(Specification $specification, MatchersCollection $matchers);
}
