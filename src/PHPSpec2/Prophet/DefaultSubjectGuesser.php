<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\SpecificationInterface;
use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Prophet\ObjectProphet;
use PHPSpec2\Subject\LazyObject;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class DefaultSubjectGuesser implements SubjectGuesserInterface
{
    private $unwrapper;

    public function __construct(ArgumentsUnwrapper $unwrapper)
    {
        $this->unwrapper = $unwrapper;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(SpecificationInterface $specification)
    {
        return true;
    }

    public function guess(SpecificationInterface $specification, MatchersCollection $matchers)
    {
        $class = preg_replace("|^spec\\\|", '', get_class($specification));

        return new ObjectProphet(new LazyObject($class), $matchers, $this->unwrapper);
    }
}
