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
        $this->shouldBeAnInstanceOf('PHPSpec2\Initializer\SpecificationInitializerInterface');
    }

    function it_should_have_zero_priority()
    {
        $this->getPriority()->shouldReturn(0);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function it_should_support_any_specification($specification)
    {
        $this->supports($specification)->shouldReturn(true);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification  $specification
     * @param PHPSpec2\Matcher\MatchersCollection $matchers
     */
    function it_should_add_some_default_matchers($specification, $example, $matchers)
    {
        $matchers->add(ANY_ARGUMENT)->shouldBeCalled();

        $this->initialize($specification, $matchers);
    }
}
