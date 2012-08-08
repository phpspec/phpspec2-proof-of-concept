<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;
use stdClass;

class ThrowMatcher implements Specification
{
    function supports_the_throw_alias_for_object_and_exception_name()
    {
        $this->object->supports('throw', new stdClass, array('\Exception'))
                ->should_be_true();
    }

    function can_specify_a_method_during_which_an_exception_should_be_throw($subject)
    {
        $subject->is_a_mock_of('stdClass');
        $subject->someMethod()->should_throw('\Exception');

        $this->object->positiveMatch('throw', $subject, array('\Exception'))
                ->during('someMethod', array());
    }

    function can_specify_a_method_during_which_an_exception_should_not_be_throw($subject)
    {
        $subject->is_a_mock_of('stdClass');
        $subject->someMethod();

        $this->object->negativeMatch('throw', $subject, array('\Exception'))
                ->during('someMethod', array());
    }

}
