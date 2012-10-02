<?php

namespace spec\PHPSpec2\Event;

use PHPSpec2\ObjectBehavior;

class SuiteEvent extends ObjectBehavior
{
    function it_should_provide_time_if_set()
    {
        $this->initializedWith($time = time());
        $this->getTime()->shouldReturn($time);
    }

    function it_should_provide_result_if_set()
    {
        $this->initializedWith(null, 2);
        $this->getResult()->shouldReturn(2);
    }
}
