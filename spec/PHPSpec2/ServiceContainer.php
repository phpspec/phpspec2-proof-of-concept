<?php

namespace spec\PHPSpec2;

use PHPSpec2\ObjectBehavior;

class ServiceContainer extends ObjectBehavior
{
    function it_should_be_able_to_set_simple_parameters()
    {
        $this->set('param', 'test#value');
        $this->get('param')->shouldReturn('test#value');
    }

    function it_should_be_able_to_set_array_parameters()
    {
        $this->set('param', array('test#value'));
        $this->get('param')->shouldReturn(array('test#value'));
    }

    /**
     * @param stdClass $service
     */
    function it_should_be_able_to_set_services($service)
    {
        $this->set('service', $service);
        $this->get('service')->shouldReturn($service);
    }

    function it_should_return_true_for_existing_parameters()
    {
        $this->set('param1', true);
        $this->has('param1')->shouldReturn(true);
    }

    function it_should_return_false_for_non_existing_parameters()
    {
        $this->has('unexisting')->shouldReturn(false);
    }

    function it_should_throw_exception_when_trying_to_get_unexisting_parameter()
    {
        $this->shouldThrow('InvalidArgumentException')->duringGet('unexisting');
    }

    /**
     * @param stdClass $service
     */
    function it_should_build_services_through_factories($service)
    {
        $this->set('service', function() use($service) {
            return $service->getWrappedSubject();
        });
        $this->get('service')->shouldReturn($service);
    }

    function it_should_be_able_to_remove_existing_parameters()
    {
        $this->set('param', true);
        $this->remove('param');
        $this->has('param')->shouldReturn(false);
    }

    function it_should_extend_collections()
    {
        $this->set('collection', array());
        $this->extend('collection', 'item1');
        $this->extend('collection', 'item2');

        $this->get('collection')->shouldReturn(array('item1', 'item2'));
    }

    function it_should_resolve_factory_collections()
    {
        $this->set('collection', array());
        $this->extend('collection', function() { return 'item1'; });
        $this->extend('collection', function() { return 'item2'; });

        $this->get('collection')->shouldReturn(array('item1', 'item2'));
    }

    function it_should_be_invokable()
    {
        $this('param', 'test#param');
        $this('param')->shouldReturn('test#param');
    }

    function it_should_implement_array_access()
    {
        $this->shouldHaveType('ArrayAccess');
    }
}
