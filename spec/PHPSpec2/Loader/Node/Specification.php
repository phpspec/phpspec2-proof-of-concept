<?php

namespace spec\PHPSpec2\Loader\Node;

use PHPSpec2\ObjectBehavior;

class Specification extends ObjectBehavior
{
    function described_with()
    {
        $this->isInitializedWith('test spec');
    }

    function it_should_have_title()
    {
        $this->getTitle()->shouldReturn('test spec');
    }

    function it_should_not_have_children_by_default()
    {
        $this->getChildren()->shouldHaveCount(0);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example $child
     */
    function it_could_have_childs($child)
    {
        $this->addChild($child);
        $this->getChildren()->shouldHaveCount(1);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $child
     */
    function its_child_could_be_another_specification($child)
    {
        $this->addChild($child);
        $this->getChildren()->shouldHaveCount(1);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $child1
     * @param PHPSpec2\Loader\Node\Example       $child2
     */
    function it_could_have_many_childs($child1, $child2)
    {
        $this->addChild($child1);
        $this->addChild($child2);
        $this->getChildren()->shouldHaveCount(2);
    }

    /**
     * @param PHPSpec2\Loader\Node\Node $child
     */
    function it_should_set_parent_on_children($child)
    {
        $child->setParent($this)->shouldBeCalled();
        $this->addChild($child);
    }

    function it_should_have_depth_of_0_by_default()
    {
        $this->getDepth()->shouldReturn(0);
    }

    /**
     * @param PHPSpec2\Loader\Node\Node $parent
     */
    function it_should_calculate_proper_depth_depending_on_parent($parent)
    {
        $this->setParent($parent);
        $parent->getDepth()->willReturn(10);

        $this->getDepth()->shouldReturn(11);
    }

    function its_subject_should_be_null_by_default()
    {
        $this->getSubject()->shouldReturn(null);
    }

    function its_subject_should_be_mutable()
    {
        $this->setSubject('Some\Class');
        $this->getSubject()->shouldReturn('Some\Class');
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $parent
     */
    function its_subject_should_be_parent_spec_if_does_not_have_own($parent)
    {
        $parent->getSubject()->willReturn('Other\Class');
        $this->setParent($parent);
        $this->getSubject()->shouldReturn('Other\Class');
    }
}
