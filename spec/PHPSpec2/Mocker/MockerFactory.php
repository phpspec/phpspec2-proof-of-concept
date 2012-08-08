<?php

namespace spec\PHPSpec2\Mocker;

use PHPSpec2\Specification;
use PHPSpec2\Stub\ArgumentsResolver;

class MockerFactory implements Specification
{
    function described_with()
    {
        $this->object ->is_an_instance_of('PHPSpec2\Mocker\MockerFactory');
    }

    function creates_a_Mockery_Mocker_by_default()
    {
        $this->object->mock('PHPSpec2\Specification')
            ->should_return_an_instance_of('PHPSpec2\Specification');
    }

    function can_mock_method_on_created_mock($resolver)
    {
        $mock = $this->object->mock('PHPSpec2\Specification');

        $mock->mockMethod('someMethid', array(), new ArgumentsResolver())
            ->should_return_an_instance_of('PHPSpec2\Mocker\Mockery\ExpectationProxy');
    }

    function can_be_created_with_an_alternative_mocker($mocker, $proxy)
    {
        $proxy        ->is_a_mock_of('PHPSpec2\Mocker\MockProxyInterface');
        $mocker       ->is_a_mock_of('PHPSpec2\Mocker\MockerInterface');
        $this->object ->is_an_instance_of('PHPSpec2\Mocker\MockerFactory', array($mocker));

        $mocker->mock('PHPSpec2\Specification')
            ->will_return($proxy);

        $this->object->mock('PHPSpec2\Specification')
            ->should_be_equal_to($proxy);
    }

    function can_be_created_with_later_configured_mocker($mocker, $proxy)
    {
        $proxy  ->is_a_mock_of('PHPSpec2\Mocker\MockProxyInterface');
        $mocker ->is_a_mock_of('PHPSpec2\Mocker\MockerInterface');

        $this->object->setMocker($mocker);

        $mocker->mock('PHPSpec2\Specification')
            ->will_return($proxy);

        $this->object->mock('PHPSpec2\Specification')
            ->should_be_equal_to($proxy);
    }
}
