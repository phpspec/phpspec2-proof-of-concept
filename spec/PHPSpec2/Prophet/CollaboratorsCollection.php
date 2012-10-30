<?php

namespace spec\PHPSpec2\Prophet;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Exception\CollaboratorNotFoundException;

class CollaboratorsCollection extends ObjectBehavior
{
    function it_should_have_no_collaborators_by_default()
    {
        $this->getAll()->shouldHaveCount(0);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet1
     * @param PHPSpec2\Prophet\ProphetInterface $prophet2
     */
    function it_should_have_all_registered_collaborators_with_names($prophet1, $prophet2)
    {
        $this->set('1', $prophet1);
        $this->set('2', $prophet2);
        $this->getAll()->shouldHaveCount(2);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet1
     * @param PHPSpec2\Prophet\ProphetInterface $prophet2
     */
    function it_should_overwrite_collaborator_with_the_same_name($prophet1, $prophet2)
    {
        $this->set('1', $prophet1);
        $this->set('1', $prophet2);
        $this->getAll()->shouldHaveCount(1);
    }

    function its_has_should_return_false_if_collaborator_not_registered()
    {
        $this->has('1')->shouldReturn(false);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet
     */
    function its_has_should_return_true_if_collaborator_registered($prophet)
    {
        $this->set('1', $prophet);
        $this->has('1')->shouldReturn(true);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet1
     * @param PHPSpec2\Prophet\ProphetInterface $prophet2
     */
    function it_should_return_collaborator_by_name($prophet1, $prophet2)
    {
        $this->set('1', $prophet1);
        $this->set('2', $prophet2);

        $this->get('1')->shouldReturn($prophet1);
        $this->get('2')->shouldReturn($prophet2);
    }

    function it_should_throw_exception_if_collaborator_not_found()
    {
        $this->shouldThrow(new CollaboratorNotFoundException('unexistent'))
            ->duringGet('unexistent');
    }
}
