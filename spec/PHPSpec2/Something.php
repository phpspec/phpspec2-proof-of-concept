<?php

namespace spec\PHPSpec2;

use PHPSpec2\ObjectBehavior;

class Something extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->object->shouldHaveType('PHPSpec2\Something');
    }
}
