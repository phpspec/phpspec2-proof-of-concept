<?php

namespace spec\PHPSpec2\Event;

use PHPSpec2\ObjectBehavior;

class ExampleEvent extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function let($example)
    {
        $this->beConstructedWith($example);
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
        $this->beConstructedWith($example, $time = time());
        $this->getTime()->shouldReturn($time + 1);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function it_should_provide_result_if_set($example)
    {
        $this->beConstructedWith($example, null, $this->PENDING);
        $this->getResult()->shouldReturn($this->PENDING - 2);
    }

    /**
     * @param PHPSpec2\Loader\Node\Example $example
     */
    function it_should_provide_exception_if_set($example)
    {
        $this->beConstructedWith($example, null, null, $exception = new \Exception);
        $this->getException()->shouldReturn($exception);
    }
}
