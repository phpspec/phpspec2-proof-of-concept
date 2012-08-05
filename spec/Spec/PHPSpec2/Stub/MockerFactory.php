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
}
