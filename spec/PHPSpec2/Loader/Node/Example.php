<?php

namespace spec\PHPSpec2\Loader\Node;

use PHPSpec2\ObjectBehavior;

class Example extends ObjectBehavior
{
    /**
     * @param ReflectionFunctionAbstract $function
     */
    function described_with($function)
    {
        $this->objectIsConstructedWith('test example', $function);
    }

    function it_should_have_title()
    {
        $this->getTitle()->shouldReturn('test example');
    }

    function it_should_have_mapped_function($function)
    {
        $this->getFunction()->shouldReturn($function);
    }

    function it_should_not_have_prefunctions_by_default()
    {
        $this->getPreFunctions()->shouldHaveCount(0);
    }

    function it_should_not_have_postfunctions_by_default()
    {
        $this->getPostFunctions()->shouldHaveCount(0);
    }

    function it_could_have_prefunctions($function)
    {
        $this->addPreFunction($function);
        $this->getPreFunctions()->shouldHaveCount(1);
    }

    function it_could_have_postfunctions($function)
    {
        $this->addPostFunction($function);
        $this->getPostFunctions()->shouldHaveCount(1);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function its_subject_should_be_calculated_from_parent($specification)
    {
        $specification->getSubject()->willReturn('Class');
        $this->setParent($specification);
        $this->getSubject()->shouldReturn('Class');
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function its_subject_should_be_null_if_theres_no_parent($specification)
    {
        $specification->getSubject()->willReturn(null);
        $this->getSubject()->shouldReturn(null);
    }
}
