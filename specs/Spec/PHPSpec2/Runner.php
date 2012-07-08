<?php

namespace Spec\PHPSpec2;

use PHPSpec2\SpecificationInterface;

class Runner implements SpecificationInterface
{
    function described_with($runner)
    {
        $runner->is_an_instance_of('PHPSpec2\Runner');
    }

    function should_be_aware_of_event_dispatcher($runner, $dispatcher)
    {
        $dispatcher->is_a_mock_of('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $runner->setEventDispatcher($dispatcher);
        $runner->getEventDispatcher()->should_be_same($dispatcher);
    }

    function should_accept_event_dispatcher_as_an_argument($runner, $dispatcher)
    {
        $dispatcher->is_a_mock_of('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $runner->is_an_instance_of('PHPSpec2\Runner', array($dispatcher));

        $runner->getEventDispatcher()->should_be_same($dispatcher);
    }
}
