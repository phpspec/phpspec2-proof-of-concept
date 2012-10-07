<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\ObjectBehavior;

class ThrowMatcher extends ObjectBehavior
{
    /**
     * @param PHPSpec2\Wrapper\ArgumentsUnwrapper             $unwrapper
     * @param PHPSpec2\Formatter\Presenter\PresenterInterface $presenter
     */
    function let($unwrapper, $presenter)
    {
        $unwrapper->unwrapAll(ANY_ARGUMENTS)->willReturnUsing(function($arguments) {
            if (!is_array($arguments[0])) {
                $arguments[0] = $arguments[0]->getWrappedSubject();
            }

            return $arguments;
        });

        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val1');
        $presenter->presentValue(ANY_ARGUMENTS)->willReturn('val2');

        $this->beConstructedWith($unwrapper, $presenter);
    }

    function it_supports_the_throw_alias_for_object_and_exception_name()
    {
        $this->supports('throw', '', array())->shouldReturn(true);
    }

    /**
     * @param Prophet $subject mock of stdClass
     */
    function it_can_specify_a_method_during_which_an_exception_should_be_throw($subject)
    {
        $subject->someMethod()->willThrow('\Exception');

        $this->positiveMatch('throw', $subject, array('\Exception'))->duringSomeMethod(array());
    }

    /**
     * @param Prophet $subject mock of stdClass
     */
    function it_can_specify_a_method_during_which_an_exception_should_not_be_throw($subject)
    {
        $this->negativeMatch('throw', $subject, array('\Exception'))->duringSomeMethod(array());
    }
}
