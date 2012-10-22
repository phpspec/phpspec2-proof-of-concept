<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\SpecificationInterface;
use PHPSpec2\Loader\Node\Example;
use PHPSpec2\Prophet\CollaboratorsCollection;
use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Matcher;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class DefaultMatchersInitializer implements InitializerInterface
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

    public function supports(SpecificationInterface $specification, Example $example)
    {
        return true;
    }

    public function initialize(SpecificationInterface $specification, Example $example,
                               CollaboratorsCollection $prophets, MatchersCollection $matchers)
    {
        $matchers->add(new Matcher\IdentityMatcher($this->presenter));
        $matchers->add(new Matcher\ComparisonMatcher($this->presenter));
        $matchers->add(new Matcher\ThrowMatcher($this->unwrapper, $this->presenter));
        $matchers->add(new Matcher\CountMatcher($this->presenter));
        $matchers->add(new Matcher\TypeMatcher($this->presenter));
        $matchers->add(new Matcher\ObjectStateMatcher($this->presenter));
    }
}
