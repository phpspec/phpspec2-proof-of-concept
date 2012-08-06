<?php

namespace spec\PHPSpec2\Stub;

use PHPSpec2\Specification;

class MockerFactory implements Specification
{
    function creates_a_Mockery_Mocker_by_default()
    {
        $this->object ->is_an_instance_of('PHPSpec2\Stub\MockerFactory');

        $this->object->mock('PHPSpec2\Specification')
            ->should_return_an_instance_of('PHPSpec2\Stub\Mocker\MockeryMock');
    }

    function can_be_created_with_an_alternative_mocker($mocker, $mock)
    {
        $mock         ->is_a_mock_of('PHPSpec2\Stub\Mocker\MockInterface');
        $mocker       ->is_a_mock_of('PHPSpec2\Stub\Mocker\MockerInterface');
        $this->object ->is_an_instance_of('PHPSpec2\Stub\MockerFactory', array($mocker));

        $mocker->mock('PHPSpec2\Specification')       ->should_return($mock);
        $this->object->mock('PHPSpec2\Specification') ->should_be_equal_to($mock);
    }
}
