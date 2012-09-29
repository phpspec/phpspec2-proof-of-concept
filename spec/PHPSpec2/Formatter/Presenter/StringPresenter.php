<?php

namespace spec\PHPSpec2\Formatter\Presenter;

use PHPSpec2\ObjectBehavior;

class StringPresenter extends ObjectBehavior
{
    function it_should_represent_short_string_by_showing_it()
    {
        $this->presentValue('some_string')->shouldReturn('[string:"some_string"]');
    }

    function it_should_represent_long_string_by_showing_its_type()
    {
        $this->presentValue('some_string_longer_than_thirty_chars')->shouldReturn('[string:...]');
    }

    function it_should_represent_integer_by_showing_it()
    {
        $this->presentValue(42)->shouldReturn('[integer:42]');
    }

    function it_should_represent_object_as_classname()
    {
        $this->presentValue(new \stdClass)->shouldReturn('[obj:stdClass]');
    }

    function it_should_represent_array_as_elements_count()
    {
        $this->presentValue(array(1, 2, 3))->shouldReturn('[array:3]');
    }

    function it_should_represent_boolean_as_string()
    {
        $this->presentValue(true)->shouldReturn('[bool:true]');
    }

    function it_should_represent_closure_as_type()
    {
        $this->presentValue(function(){})->shouldReturn('[closure]');
    }

    function it_should_represent_exception()
    {
        $this->presentValue(new \RuntimeException('message'))
            ->shouldReturn('[exc:RuntimeException("message")]');
    }

    function it_should_represent_string_as_string()
    {
        $this->presentString('some string')->shouldReturn('some string');
    }
}
