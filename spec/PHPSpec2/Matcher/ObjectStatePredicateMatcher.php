<?php

namespace spec\PHPSpec2\Matcher;

use PHPSpec2\Specification;

class ObjectStatePredicateMatcher implements Specification
{
    function supports_any_aliases_starting_with_be()
    {
        foreach ($this->a_bunch_of_aliases_starting_with_be() as $alias) {
             $this->object->supports($alias, new \stdClass, array())->should_return_true();
        }
    }

    function only_works_with_objects()
    {
        $this->object->supports('be_someone', 'no object here', array())->should_not_return_true();
    }

    function be_by_itself_is_not_enough()
    {
        $this->object->supports('be', new \stdClass, array())->should_not_return_true();
    }

    function aliases_not_starting_with_be_are_not_supported()
    {
        $this->object->supports('not_to_be', new \stdClass, array())->should_not_return_true();
    }

    function matches_is_method_against_true()
    {
        $subject = new \ReflectionClass($this);
        $this->object->should_throw('PHPSpec2\Exception\Example\FailureException')
            ->during('positiveMatch', array('be_abstract', $subject, array()));
    }

    private function a_bunch_of_aliases_starting_with_be()
    {
        return array(
            'be_someone',
            'be_awesome',
            'be_good',
            'be_not_so_good'
        );
    }

}
