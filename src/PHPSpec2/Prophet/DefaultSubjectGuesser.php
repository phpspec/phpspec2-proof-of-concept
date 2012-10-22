<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\SpecificationInterface;

class DefaultSubjectGuesser implements SubjectGuesserInterface
{
    public function getPriority()
    {
        return 0;
    }

    public function supports(SpecificationInterface $specification)
    {
        return true;
    }

    public function guess(SpecificationInterface $specification)
    {
        return preg_replace("|^spec\\\|", '', get_class($specification));
    }
}
