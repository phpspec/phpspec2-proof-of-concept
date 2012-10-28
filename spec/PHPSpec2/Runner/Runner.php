<?php

namespace spec\PHPSpec2\Runner;

use PHPSpec2\ObjectBehavior;

class Runner extends ObjectBehavior
{
    /**
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param PHPSpec2\Mocker\MockerInterface                            $mocker
     * @param PHPSpec2\Wrapper\ArgumentsUnwrapper                        $unwrapper
     */
    function let($dispatcher, $mocker, $unwrapper)
    {
        $this->beConstructedWith($dispatcher, $mocker, $unwrapper);
    }

    function it_should_not_have_initializers_registered_by_default()
    {
        $this->getSpecificationInitializers()->shouldHaveCount(0);
    }

    /**
     * @param PHPSpec2\Initializer\SpecificationInitializerInterface $specInitializer1
     * @param PHPSpec2\Initializer\SpecificationInitializerInterface $specInitializer2
     */
    function it_should_be_able_to_register_spec_initializers($specInitializer1, $specInitializer2)
    {
        $this->registerSpecificationInitializer($specInitializer1);
        $this->registerSpecificationInitializer($specInitializer2);

        $specInitializer1->getPriority()->willReturn(0);
        $specInitializer2->getPriority()->willReturn(0);

        $this->getSpecificationInitializers()->shouldHaveCount(2);
    }

    /**
     * @param PHPSpec2\Initializer\SpecificationInitializerInterface $specInitializer1
     * @param PHPSpec2\Initializer\SpecificationInitializerInterface $specInitializer2
     */
    function it_should_sort_spec_initializers_before_returning($specInitializer1, $specInitializer2)
    {
        $this->registerSpecificationInitializer($specInitializer1);
        $this->registerSpecificationInitializer($specInitializer2);

        $specInitializer1->getPriority()->willReturn(2);
        $specInitializer2->getPriority()->willReturn(1);

        $this->getSpecificationInitializers()->shouldReturn(array($specInitializer2, $specInitializer1));
    }

    /**
     * @param PHPSpec2\Prophet\SubjectGuesserInterface $guesser1
     * @param PHPSpec2\Prophet\SubjectGuesserInterface $guesser2
     */
    function it_should_be_able_to_register_subject_guesser($guesser1, $guesser2)
    {
        $this->registerSubjectGuesser($guesser1);
        $this->registerSubjectGuesser($guesser2);

        $guesser1->getPriority()->willReturn(0);
        $guesser2->getPriority()->willReturn(0);

        $this->getSubjectGuessers()->shouldHaveCount(2);
    }

    /**
     * @param PHPSpec2\Prophet\SubjectGuesserInterface $guesser1
     * @param PHPSpec2\Prophet\SubjectGuesserInterface $guesser2
     */
    function it_should_sort_guessers_before_returning($guesser1, $guesser2)
    {
        $this->registerSubjectGuesser($guesser1);
        $this->registerSubjectGuesser($guesser2);

        $guesser1->getPriority()->willReturn(5);
        $guesser2->getPriority()->willReturn(1);

        $this->getSubjectGuessers()->shouldReturn(array($guesser2, $guesser1));
    }
}
