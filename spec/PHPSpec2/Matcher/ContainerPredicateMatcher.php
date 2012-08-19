<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class ContainerPredicateMatcher implements Specification
{
    function supports_any_aliases_starting_with_have()
    {
        foreach ($this->a_bunch_of_aliases_starting_with_be() as $alias) {
             $this->object->supports($alias, new \stdClass, array())->should_return_true();
        }
    }

    function only_works_with_objects()
    {
        $this->object->supports('have_someone', 'no object here', array('could_take_an_argument'))->should_not_return_true();
    }

    function have_by_itself_is_not_enough()
    {
        $this->object->supports('have', new \stdClass, array())->should_not_return_true();
    }

    function aliases_not_starting_with_have_are_not_supported()
    {
        $this->object->supports('not_to_have', new \stdClass, array())->should_not_return_true();
    }

    function matches_has_method_against_true()
    {
        $subject = new \ReflectionClass($this);
        $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('have_method', $subject, array('unknownMethod')));
    }

    private function a_bunch_of_aliases_starting_with_be()
    {
        return array(
            'have_node',
            'have_paypal_method',
            'have_children',
            'have_belong_to_us'
        );
    }

}
