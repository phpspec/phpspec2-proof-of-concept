<?php

namespace spec\PHPSpec2\Loader\Node;

use PHPSpec2\Specification as SpecificationInterface;

class Specification implements SpecificationInterface
{
    function described_with()
    {
        $this->specification->isAnInstanceOf(
            'PHPSpec2\Loader\Node\Specification', array('test spec')
        );
    }

    function it_should_have_title()
    {
        $this->specification->getTitle()->shouldReturn('test spec');
    }

    function it_should_not_have_children_by_default()
    {
        $this->specification->getChildren()->shouldHaveCount(0);
    }

    /**
     * @param Prophet $child mock of PHPSpec2\Loader\Node\Example
     */
    function it_could_have_childs($child)
    {
        $this->specification->addChild($child);
        $this->specification->getChildren()->shouldHaveCount(1);
    }

    /**
     * @param Prophet $child mock of PHPSpec2\Loader\Node\Specification
     */
    function its_child_could_be_another_specification($child)
    {
        $this->specification->addChild($child);
        $this->specification->getChildren()->shouldHaveCount(1);
    }

    /**
     * @param Prophet $child1 mock of PHPSpec2\Loader\Node\Specification
     * @param Prophet $child2 mock of PHPSpec2\Loader\Node\Example
     */
    function it_could_have_many_childs($child1, $child2)
    {
        $this->specification->addChild($child1);
        $this->specification->addChild($child2);
        $this->specification->getChildren()->shouldHaveCount(2);
    }

    /**
     * @param Prophet $child mock of PHPSpec2\Loader\Node\Node
     */
    function it_should_set_parent_on_children($child)
    {
        $child->setParent($this->specification)->shouldBeCalled();
        $this->specification->addChild($child);
    }

    function it_should_have_depth_of_1_by_default()
    {
        $this->specification->getDepth()->shouldReturn(1);
    }

    /**
     * @param Prophet $parent mock of PHPSpec2\Loader\Node\Node
     */
    function it_should_calculate_proper_depth_depending_on_parent($parent)
    {
        $this->specification->setParent($parent);
        $parent->getDepth()->willReturn(10);

        $this->specification->getDepth()->shouldReturn(11);
    }

    function its_subject_should_be_null_by_default()
    {
        $this->specification->getSubject()->shouldReturn(null);
    }

    function its_subject_should_be_mutable()
    {
        $this->specification->setSubject('Some\Class');
        $this->specification->getSubject()->shouldReturn('Some\Class');
    }

    /**
     * @param Prophet $parent mock of PHPSpec2\Loader\Node\Specification
     */
    function its_subject_should_be_parent_spec_if_does_not_have_own($parent)
    {
        $parent->getSubject()->willReturn('Other\Class');
        $this->specification->setParent($parent);
        $this->specification->getSubject()->shouldReturn('Other\Class');
    }
}
