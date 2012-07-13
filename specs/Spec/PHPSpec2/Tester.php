<?php

namespace Spec\PHPSpec2;

use PHPSpec2\SpecificationInterface;

use ReflectionMethod;

class Tester implements SpecificationInterface
{
    function described_with($dispatcher, $tester)
    {
        $dispatcher->is_a_mock_of('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $tester->is_an_instance_of('PHPSpec2\Tester', array($dispatcher));
    }

    function accepts_event_dispatcher_as_a_constructor_argument($tester, $dispatcher)
    {
        $tester->getEventDispatcher()->should_equal($dispatcher);
    }
}
