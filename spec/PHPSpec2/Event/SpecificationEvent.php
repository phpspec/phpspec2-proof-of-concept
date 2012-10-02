<?php

namespace spec\PHPSpec2\Event;

use PHPSpec2\ObjectBehavior;

class SpecificationEvent extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function described_with($specification)
    {
        $this->initializedWith($specification);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function it_should_be_mapped_to_specification($specification)
    {
        $this->getSpecification()->shouldReturn($specification);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function it_should_provide_time_if_set($specification)
    {
        $this->initializedWith($specification, $time = time());
        $this->getTime()->shouldReturn($time);
    }

    /**
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function it_should_provide_result_if_set($specification)
    {
        $this->initializedWith($specification, null, 2);
        $this->getResult()->shouldReturn(2);
    }
}
