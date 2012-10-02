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
        $this->initializedWith('test example', 'test subject', $function);
    }

    function it_should_have_title()
    {
        $this->getTitle()->shouldReturn('test example');
    }

    function it_should_have_subject()
    {
        $this->getSubject()->shouldReturn('test subject');
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

    function it_should_not_have_specification_by_default()
    {
        $this->getSpecification()->shouldReturn(null);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $specification1
     * @param PHPSpec2\Loader\Node\Specification $specification2
     */
    function it_should_return_first_parent_specification($specification1, $specification2)
    {
        $specification1->getParent()->willReturn($specification2);
        $this->setParent($specification1);

        $this->getSpecification()->shouldReturn($specification1);
    }
}
