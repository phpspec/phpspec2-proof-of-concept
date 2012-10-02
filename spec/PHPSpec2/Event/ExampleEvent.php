<?php

namespace spec\PHPSpec2\Event;

use PHPSpec2\ObjectBehavior;

class ExampleEvent extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function described_with($example)
    {
        $this->initializedWith($example);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function it_should_be_mapped_to_example($example)
    {
        $this->getExample()->shouldReturn($example);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example       $example
     * @param PHPSpec2\Loader\Node\Specification $specification
     */
    function it_should_provide_specification_from_example($example, $specification)
    {
        $example->getSpecification()->willReturn($specification);
        $this->getSpecification()->shouldReturn($specification);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function it_should_provide_time_if_set($example)
    {
        $this->initializedWith($example, $time = time());
        $this->getTime()->shouldReturn($time);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function it_should_provide_result_if_set($example)
    {
        $this->initializedWith($example, null, $this->PENDING);
        $this->getResult()->shouldReturn($this->PENDING);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function it_should_provide_exception_if_set($example)
    {
        $this->initializedWith($example, null, null, $exception = new \Exception);
        $this->getException()->shouldReturn($exception);
    }
}
