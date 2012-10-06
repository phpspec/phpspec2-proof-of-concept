<?php

namespace spec\PHPSpec2\Formatter\Presenter;

use PHPSpec2\ObjectBehavior;

class StringPresenter extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Formatter\Presenter\Differ\Differ $differ
     */
    function let($differ)
    {
        $this->beConstructedWith($differ);
    }

    function it_should_present_short_string_in_quotes()
    {
        $this->presentValue('some_string')->shouldReturn('"some_string"');
    }

    function it_should_present_long_string_in_quotes_but_trimmed()
    {
        $this->presentValue('some_string_longer_than_twenty_five_chars')
            ->shouldReturn('"some_string_longer_than_t"...');
    }

    function it_should_present_only_first_line_of_multiline_string()
    {
        $this->presentValue("some\nmultiline\nvalue")->shouldReturn('"some"...');
    }

    function it_should_present_simple_type_as_typed_value()
    {
        $this->presentValue(42)->shouldReturn('[integer:42]');
        $this->presentValue(42.0)->shouldReturn('[double:42]');
    }

    function it_should_present_object_as_classname()
    {
        $this->presentValue(new \stdClass)->shouldReturn('[obj:stdClass]');
    }

    function it_should_present_array_as_elements_count()
    {
        $this->presentValue(array(1, 2, 3))->shouldReturn('[array:5]');
    }

    function it_should_present_boolean_as_string()
    {
        throw new \Exception('shit');
        $this->presentValue(true)->shouldReturn('true');
    }

    function it_should_present_closure_as_type()
    {
        $this->presentValue(function(){})->shouldReturn('[closure]');
    }

    function it_should_present_exception_as_class_with_constructor()
    {
        $this->presentValue(new \RuntimeException('message'))
            ->shouldReturn('[exc:RuntimeException("message")]');
    }

    function it_should_present_string_as_string()
    {
        $this->presentString('some string')->shouldReturn('some string');
    }
}
