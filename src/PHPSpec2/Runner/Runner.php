<?php

namespace PHPSpec2\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionFunctionAbstract;

use PHPSpec2\Loader\Node;
use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Event;
use PHPSpec2\Prophet;
use PHPSpec2\Matcher;
use PHPSpec2\Initializer;
use PHPSpec2\Exception\Example as ExampleException;
use PHPSpec2\SpecificationInterface;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class Runner
{
    private $eventDispatcher;
    private $mocker;
    private $presenter;

    private $guessers = array();
    private $guessersSorted = false;
    private $specInitializers = array();
    private $specInitializersSorted = false;
    private $exampleInitializers = array();
    private $exampleInitializersSorted = false;

    public function __construct(EventDispatcherInterface $dispatcher, MockerInterface $mocker,
                                PresenterInterface $presenter)
    {
        $this->eventDispatcher = $dispatcher;
        $this->mocker          = $mocker;
        $this->presenter       = $presenter;
    }

    public function registerSpecificationInitializer(Initializer\SpecificationInitializerInterface $initializer)
    {
        $this->specInitializers[]     = $initializer;
        $this->specInitializersSorted = false;
    }

    public function getSpecificationInitializers()
    {
        if (0 != count($this->specInitializers) && !$this->specInitializersSorted) {
            @usort($this->specInitializers, function($init1, $init2) {
                return strnatcmp($init1->getPriority(), $init2->getPriority());
            });

            $this->specInitializersSorted = true;
        }

        return $this->specInitializers;
    }

    public function registerExampleInitializer(Initializer\ExampleInitializerInterface $initializer)
    {
        $this->exampleInitializers[]     = $initializer;
        $this->exampleInitializersSorted = false;
    }

    public function getExampleInitializers()
    {
        if (0 != count($this->exampleInitializers) && !$this->exampleInitializersSorted) {
            @usort($this->exampleInitializers, function($init1, $init2) {
                return strnatcmp($init1->getPriority(), $init2->getPriority());
            });

            $this->exampleInitializersSorted = true;
        }

        return $this->exampleInitializers;
    }

    public function registerSubjectGuesser(Prophet\SubjectGuesserInterface $guesser)
    {
        $this->guessers[] = $guesser;
    }

    public function getSubjectGuessers()
    {
        if (0 !== count($this->guessers) && !$this->guessersSorted) {
            @usort($this->guessers, function($guesser1, $guesser2) {
                return strnatcmp($guesser1->getPriority(), $guesser2->getPriority());
            });
        }

        return $this->guessers;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function runSpecification(Node\Specification $specification)
    {
        if (defined('PHPSPEC_ERROR_REPORTING')) {
            $errorLevel = PHPSPEC_ERROR_REPORTING;
        } else {
            $errorLevel = E_ALL ^ E_STRICT;
        }
        $oldHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        $matchers = new Matcher\MatchersCollection($this->presenter);
        foreach ($this->getSpecificationInitializers() as $initializer) {
            if ($initializer->supports($specification)) {
                $initializer->initialize($specification, $matchers);
            }
        }

        $this->eventDispatcher->dispatch('beforeSpecification',
            new Event\SpecificationEvent($specification)
        );
        $startTime = microtime(true);

        $result = Event\ExampleEvent::PASSED;
        foreach ($specification->getChildren() as $child) {
            $result = max($result, $this->runExample($child, $matchers));
        }

        $this->eventDispatcher->dispatch('afterSpecification',
            new Event\SpecificationEvent($specification, microtime(true) - $startTime, $result)
        );

        if (null !== $oldHandler) {
            set_error_handler($oldHandler);
        }

        return $result;
    }

    public function runExample(Node\Example $example, Matcher\MatchersCollection $matchers)
    {
        $context       = $example->getFunction()->getDeclaringClass()->newInstance();
        $collaborators = new Prophet\CollaboratorsCollection($this->presenter);

        foreach ($this->getExampleInitializers() as $initializer) {
            if ($initializer->supports($context, $example)) {
                $initializer->initialize($context, $example, $collaborators);
            }
        }

        foreach ($this->getSubjectGuessers() as $guesser) {
            if ($guesser->supports($context)) {
                $context->setProphet($guesser->guess($context, $matchers));
                break;
            }
        }

        $this->eventDispatcher->dispatch('beforeExample',
            new Event\ExampleEvent($example)
        );
        $startTime = microtime(true);

        try {
            foreach ($example->getPreFunctions() as $preFunction) {
                $this->invoke($context, $preFunction, $collaborators);
            }

            $this->invoke($context, $example->getFunction(), $collaborators);

            foreach ($example->getPostFunctions() as $postFunction) {
                $this->invoke($context, $postFunction, $collaborators);
            }

            $this->mocker->verify();

            $status    = Event\ExampleEvent::PASSED;
            $exception = null;
        } catch (ExampleException\PendingException $e) {
            $status    = Event\ExampleEvent::PENDING;
            $exception = $e;
        } catch (ExampleException\FailureException $e) {
            $status    = Event\ExampleEvent::FAILED;
            $exception = $e;
        } catch (\Exception $e) {
            $status    = Event\ExampleEvent::BROKEN;
            $exception = $e;
        }

        $runTime = microtime(true) - $startTime;
        $this->eventDispatcher->dispatch('afterExample',
            $event = new Event\ExampleEvent($example, $runTime, $status, $exception)
        );

        return $event->getResult();
    }

    private function invoke(SpecificationInterface $context, ReflectionFunctionAbstract $function,
                            Prophet\CollaboratorsCollection $collaborators)
    {
        $parameters = array();
        foreach ($function->getParameters() as $parameter) {
            $parameters[] = $collaborators->get($parameter->getName());
        }

        $function->invokeArgs($context, $parameters);
    }

    /**
     * Custom error handler.
     *
     * This method used as custom error handler when step is running.
     *
     * @see set_error_handler()
     *
     * @param integer $level
     * @param string  $message
     * @param string  $file
     * @param integer $line
     *
     * @return Boolean
     *
     * @throws ErrorException
     */
    final public function errorHandler($level, $message, $file, $line)
    {
        if (0 !== error_reporting()) {
            throw new ExampleException\ErrorException($level, $message, $file, $line);
        }

        // error reporting turned off or more likely suppressed with @
        return false;
    }
}
