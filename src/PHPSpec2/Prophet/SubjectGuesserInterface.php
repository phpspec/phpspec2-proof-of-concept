<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\SpecificationInterface;
use PHPSpec2\Matcher\MatchersCollection;

interface SubjectGuesserInterface
{
    public function getPriority();
    public function supports(SpecificationInterface $specification);
    public function guess(SpecificationInterface $specification, MatchersCollection $matchers);
}
