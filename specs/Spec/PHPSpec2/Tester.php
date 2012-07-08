<?php

namespace Spec\PHPSpec2;

use PHPSpec2\SpecificationInterface;

use ReflectionMethod;

class Tester implements SpecificationInterface
{
    function described_with($tester)
    {
        $tester->is_an_instance_of('PHPSpec2\Tester');
    }

    function is_aware_of_event_dispatcher($tester, $dispatcher)
    {
        $dispatcher->is_a_mock_of('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $tester->setEventDispatcher($dispatcher);
        $tester->getEventDispatcher()->should_equal($dispatcher);
    }

    function accepts_event_dispatcher_as_a_constructor_argument($tester, $dispatcher)
    {
        $dispatcher->is_a_mock_of('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $tester->is_an_instance_of('PHPSpec2\Tester', array($dispatcher));

        $tester->getEventDispatcher()->should_equal($dispatcher);
    }

    function tests_green_example_by_specification_reflection($tester, $spec)
    {
        $spec->is_a_mock_of('ReflectionClass');

        $tester->test($spec);
    }
}
