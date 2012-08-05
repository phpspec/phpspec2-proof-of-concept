<?php

namespace Spec\PHPSpec2\Stub;

use PHPSpec2\Specification;

class MockerFactory implements Specification
{
    function creates_a_Mockery_Mocker_by_default()
    {
        $this->object->is_an_instance_of('PHPSpec2\Stub\MockerFactory');
        $this->object->createFor('PHPSpec2\Specification')
            ->should_return_an_instance_of('PHPSpec2\Stub\Mocker\MockeryMocker');
    }

    function can_be_created_with_an_alternative_mocker($mockerCreator, $mocker)
    {
        $mocker->is_a_mock_of('PHPSpec2\Stub\Mocker\MockerInterface');

        $mockerCreator->is_a_mock_of('PHPSpec2\Stub\Mocker\MockerCreatorInterface');
        $mockerCreator->createNew('PHPSpec2\Specification')->should_return($mocker);

        $this->object->is_an_instance_of('PHPSpec2\Stub\MockerFactory', array($mockerCreator));
        $this->object->createFor('PHPSpec2\Specification')->should_be_equal_to($mocker);
    }
}
