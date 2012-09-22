<?php

namespace spec\PHPSpec2\Formatter\Representer;

use PHPSpec2\MethodBehavior;

class BasicRepresenter_representValue extends MethodBehavior
{
    function it_should_represent_short_string_by_showing_it()
    {
        $this('some_string')->shouldReturn('"some_string"');
    }

    function it_should_represent_long_string_by_showing_its_type()
    {
        $this('some_string_longer_than_thirty_characters')->shouldReturn('[string]');
    }

    function it_should_represent_integer_by_showing_it()
    {
        $this(42)->shouldReturn('integer(42)');
    }

    function it_should_represent_object_as_classname()
    {
        $this(new \stdClass)->shouldReturn('object(stdClass)');
    }

    function it_should_represent_array_as_elements_count()
    {
        $this(array(1, 2, 3))->shouldReturn('array(3)');
    }

    function it_should_represent_boolean_as_string()
    {
        $this(true)->shouldReturn('true');
    }

    function it_should_represent_closure_as_type()
    {
        $this(function(){})->shouldReturn('[closure]');
    }
}
