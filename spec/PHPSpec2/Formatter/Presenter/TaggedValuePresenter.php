<?php

namespace spec\PHPSpec2\Formatter\Presenter;

use PHPSpec2\ObjectBehavior;

class TaggedValuePresenter extends ObjectBehavior
{
    function it_should_wrap_value_into_tags()
    {
        $this->presentValue('string')->shouldReturn('<value>"string"</value>');
    }

    function it_should_wrap_string_into_tags()
    {
        $this->presentString('string')->shouldReturn('<value>string</value>');
    }
}
