<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\SpecificationInterface;

interface SubjectGuesserInterface
{
    public function getPriority();
    public function supports(SpecificationInterface $specification);
    public function guess(SpecificationInterface $specification);
}
