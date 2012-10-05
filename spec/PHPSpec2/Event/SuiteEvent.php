<?php

namespace spec\PHPSpec2\Event;

use PHPSpec2\ObjectBehavior;

class SuiteEvent extends ObjectBehavior
{
    function it_should_provide_time_if_set()
    {
        $this->beConstructedWith($time = time());
        $this->getTime()->shouldReturn($time);
    }

    function it_should_provide_result_if_set()
    {
        $this->beConstructedWith(null, 2);
        $this->getResult()->shouldReturn(2);
    }
}
