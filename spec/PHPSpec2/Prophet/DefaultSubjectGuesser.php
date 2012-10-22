<?php

namespace spec\PHPSpec2\Prophet;

use PHPSpec2\ObjectBehavior;

class DefaultSubjectGuesser extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('PHPSpec2\Prophet\SubjectGuesserInterface');
    }

    function it_should_have_zero_priority()
    {
        $this->getPriority()->shouldReturn(0);
    }

    /**
     * @param PHPSpec2\SpecificationInterface $specification
     */
    function it_should_support_any_specification($specification)
    {
        $this->supports($specification)->shouldReturn(true);
    }
}
