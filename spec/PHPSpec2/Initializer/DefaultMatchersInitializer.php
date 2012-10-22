<?php

namespace spec\PHPSpec2\Initializer;

use PHPSpec2\ObjectBehavior;

class DefaultMatchersInitializer extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\PresenterInterface $presenter
     * @param PHPSpec2\Wrapper\ArgumentsUnwrapper             $unwrapper
     */
    function let($presenter, $unwrapper)
    {
        $this->beConstructedWith($presenter, $unwrapper);
    }

    function it_should_implement_InitializerInterface()
    {
        $this->shouldBeAnInstanceOf('PHPSpec2\Initializer\InitializerInterface');
    }

    function it_should_have_zero_priority()
    {
        $this->getPriority()->shouldReturn(0);
    }

    /**
     * @param PHPSpec2\SpecificationInterface $specification
     * @param PHPSpec2\Loader\Node\Example    $example
     */
    function it_should_support_any_specification($specification, $example)
    {
        $this->supports($specification, $example)->shouldReturn(true);
    }

    /**
     * @param PHPSpec2\SpecificationInterface     $specification
     * @param PHPSpec2\Loader\Node\Example        $example
     * @param PHPSpec2\Prophet\ProphetsCollection $prophets
     * @param PHPSpec2\Matcher\MatchersCollection $matchers
     */
    function it_should_add_some_default_matchers($specification, $example, $prophets, $matchers)
    {
        $matchers->add(ANY_ARGUMENT)->shouldBeCalled();

        $this->initialize($specification, $example, $prophets, $matchers);
    }
}
