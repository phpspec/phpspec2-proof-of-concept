<?php

namespace spec\PHPSpec2\Mocker;

use PHPSpec2\Specification;
use PHPSpec2\Stub\ArgumentsResolver;

class MockerFactory implements Specification
{
    function it_creates_a_Mockery_Mocker_by_default()
    {
        $this->object->mock('PHPSpec2\Specification')
            ->shouldReturnAnInstanceOf('PHPSpec2\Specification');
    }

    function it_can_mock_method_on_created_mock($resolver)
    {
        $mock = $this->object->mock('PHPSpec2\Specification');

        $mock->mockMethod('someMethid', array(), new ArgumentsResolver())
            ->shouldReturnAnInstanceOf('PHPSpec2\Mocker\Mockery\ExpectationProxy');
    }
}
