<?php

namespace spec\PHPSpec2\Prophet;

use PHPSpec2\ObjectBehavior;

class ProphetsCollection extends ObjectBehavior
{
    function it_should_have_no_collaborators_by_default()
    {
        $this->getCollaborators()->shouldHaveCount(0);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet1
     * @param PHPSpec2\Prophet\ProphetInterface $prophet2
     */
    function it_should_have_all_registered_collaborators_with_names($prophet1, $prophet2)
    {
        $this->setCollaborator('1', $prophet1);
        $this->setCollaborator('2', $prophet2);
        $this->getCollaborators()->shouldHaveCount(2);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet1
     * @param PHPSpec2\Prophet\ProphetInterface $prophet2
     */
    function it_should_overwrite_collaborator_with_the_same_name($prophet1, $prophet2)
    {
        $this->setCollaborator('1', $prophet1);
        $this->setCollaborator('1', $prophet2);
        $this->getCollaborators()->shouldHaveCount(1);
    }

    function its_hasCollaborator_should_return_false_if_collaborator_not_registered()
    {
        $this->hasCollaborator('1')->shouldReturn(false);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet
     */
    function its_hasCollaborator_should_return_true_if_collaborator_registered($prophet)
    {
        $this->setCollaborator('1', $prophet);
        $this->hasCollaborator('1')->shouldReturn(true);
    }

    /**
     * @param PHPSpec2\Prophet\ProphetInterface $prophet1
     * @param PHPSpec2\Prophet\ProphetInterface $prophet2
     */
    function it_should_return_collaborator_by_name($prophet1, $prophet2)
    {
        $this->setCollaborator('1', $prophet1);
        $this->setCollaborator('2', $prophet2);

        $this->getCollaborator('1')->shouldReturn($prophet1);
        $this->getCollaborator('2')->shouldReturn($prophet2);
    }
}
