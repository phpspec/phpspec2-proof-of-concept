<?php

namespace spec\PHPSpec2\Initializer;

use PHPSpec2\ObjectBehavior;

class CustomMatchersInitializer extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     * @param ReflectionClass                    $class
     */
    function let($specification, $class)
    {
        $specification->getClass()->willReturn($class);
        $class->getName()->willReturn('Custom\Specification');
    }

    function it_should_be_specification_initializer()
    {
        $this->shouldBeAnInstanceOf('PHPSpec2\Initializer\SpecificationInitializerInterface');
    }

    function it_should_support_matcher_providers($specification, $class)
    {
        $class->implementsInterface('PHPSpec2\Matcher\CustomMatchersProviderInterface')
            ->willReturn(true);

        $this->supports($specification)->shouldReturn(true);
    }

    function it_should_not_support_anything_except_matcher_providers($specification, $class)
    {
        $class->implementsInterface('PHPSpec2\Matcher\CustomMatchersProviderInterface')
            ->willReturn(false);

        $this->supports($specification)->shouldReturn(false);
    }

    /**
     * @param ReflectionMethod                    $method
     * @param PHPSpec2\Matcher\MatchersCollection $matchers
     * @param PHPSpec2\Matcher\MatcherInterface   $matcher1
     * @param PHPSpec2\Matcher\MatcherInterface   $matcher2
     */
    function it_should_initialize_matchers_from_provider($specification, $class, $method,
                                                         $matchers, $matcher1, $matcher2)
    {
        $class->getMethod('getMatchers')->willReturn($method);
        $method->invokeArgs(null, array())->willReturn(array($matcher1, $matcher2));

        $matchers->add($matcher1)->shouldBeCalled();
        $matchers->add($matcher2)->shouldBeCalled();

        $this->initialize($specification, $matchers);
    }
}
