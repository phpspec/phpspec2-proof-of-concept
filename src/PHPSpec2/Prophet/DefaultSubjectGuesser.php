<?php

namespace PHPSpec2\Prophet;

use PHPSpec2\SpecificationInterface;
use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Prophet\ObjectProphet;
use PHPSpec2\Subject\LazyObject;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class DefaultSubjectGuesser implements SubjectGuesserInterface
{
    private $unwrapper;
    private $presenter;

    public function __construct(ArgumentsUnwrapper $unwrapper, PresenterInterface $presenter)
    {
        $this->unwrapper = $unwrapper;
        $this->presenter = $presenter;
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

        return new ObjectProphet(
            new LazyObject($class, array(), $this->presenter),
            $matchers,
            $this->unwrapper,
            $this->presenter
        );
    }
}
