<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\Loader\Node\Specification;
use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class CustomMatchersInitializer implements SpecificationInitializerInterface
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
        return $specification->getClass()->implementsInterface(
            'PHPSpec2\Matcher\CustomMatchersProviderInterface'
        );
    }

    public function initialize(Specification $specification, MatchersCollection $collection)
    {
        $class    = $specification->getClass()->getName();
        $matchers = $specification->getClass()->getMethod('getMatchers')->invoke(
            $class, $this->presenter, $this->unwrapper
        );

        foreach ($matchers as $matcher) {
            $collection->add($matcher);
        }
    }
}
