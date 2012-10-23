<?php

namespace spec\PHPSpec2\Runner;

use PHPSpec2\ObjectBehavior;

class Runner extends ObjectBehavior
{
    /**
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param PHPSpec2\Matcher\MatchersCollection                        $matchers
     * @param PHPSpec2\Mocker\MockerInterface                            $mocker
     * @param PHPSpec2\Wrapper\ArgumentsUnwrapper                        $unwrapper
     */
    function let($dispatcher, $matchers, $mocker, $unwrapper)
    {
        $this->beConstructedWith($dispatcher, $matchers, $mocker, $unwrapper);
    }

    function it_should_not_have_initializers_registered_by_default()
    {
        $this->getInitializers()->shouldHaveCount(0);
    }

    /**
     * @param PHPSpec2\Initializer\InitializerInterface $initializer1
     * @param PHPSpec2\Initializer\InitializerInterface $initializer2
     */
    function it_should_be_able_to_register_initializers($initializer1, $initializer2)
    {
        $this->registerInitializer($initializer1);
        $this->registerInitializer($initializer2);

        $initializer1->getPriority()->willReturn(0);
        $initializer2->getPriority()->willReturn(0);

        $this->getInitializers()->shouldHaveCount(2);
    }

    /**
     * @param PHPSpec2\Initializer\InitializerInterface $initializer1
     * @param PHPSpec2\Initializer\InitializerInterface $initializer2
     */
    function it_should_sort_initializers_before_returning($initializer1, $initializer2)
    {
        $this->registerInitializer($initializer1);
        $this->registerInitializer($initializer2);

        $initializer1->getPriority()->willReturn(2);
        $initializer2->getPriority()->willReturn(1);

        $this->getInitializers()->shouldReturn(array($initializer2, $initializer1));
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
