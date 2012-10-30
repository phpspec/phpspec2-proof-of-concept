<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\Loader\Node\Specification;
use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Matcher;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class DefaultMatchersInitializer implements SpecificationInitializerInterface
{
    private $presenter;
    private $unwrapper;

    public function __construct(PresenterInterface $presenter, ArgumentsUnwrapper $unwrapper)
    {
        $this->presenter = $presenter;
        $this->unwrapper = $unwrapper;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(Specification $specification)
    {
        return true;
    }

    public function initialize(Specification $specification, MatchersCollection $matchers)
    {
        $matchers->add(new Matcher\IdentityMatcher($this->presenter));
        $matchers->add(new Matcher\ComparisonMatcher($this->presenter));
        $matchers->add(new Matcher\ThrowMatcher($this->unwrapper, $this->presenter));
        $matchers->add(new Matcher\CountMatcher($this->presenter));
        $matchers->add(new Matcher\TypeMatcher($this->presenter));
        $matchers->add(new Matcher\ObjectStateMatcher($this->presenter));
        $matchers->add(new Matcher\ScalarMatcher($this->presenter));
    }
}
