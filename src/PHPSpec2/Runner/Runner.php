<?php

namespace PHPSpec2\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionFunctionAbstract;

use PHPSpec2\Loader\Node;
use PHPSpec2\Event;
use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Subject\LazyObject;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Prophet;
use PHPSpec2\Matcher;
use PHPSpec2\Initializer;
use PHPSpec2\Exception\Example as ExampleException;

class Runner
{
    private $eventDispatcher;
    private $mocker;
    private $unwrapper;

    private $guessers = array();
    private $guessersSorted = false;
    private $initializers = array();
    private $initializersSorted = false;

    public function __construct(EventDispatcherInterface $dispatcher, MockerInterface $mocker,
                                ArgumentsUnwrapper $unwrapper)
    {
        $this->eventDispatcher = $dispatcher;
        $this->mocker          = $mocker;
        $this->unwrapper       = $unwrapper;
    }

    public function registerInitializer(Initializer\InitializerInterface $initializer)
    {
        $this->initializers[]     = $initializer;
        $this->initializersSorted = false;
    }

    public function getInitializers()
    {
        if (0 != count($this->initializers) && !$this->initializersSorted) {
            @usort($this->initializers, function($init1, $init2) {
                return strnatcmp($init1->getPriority(), $init2->getPriority());
            });

            $this->initializersSorted = true;
        }

        return $this->initializers;
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

        $this->eventDispatcher->dispatch('beforeSpecification',
            new Event\SpecificationEvent($specification)
        );
        $startTime = microtime(true);

        $result = Event\ExampleEvent::PASSED;
        foreach ($specification->getChildren() as $child) {
            $result = max($result, $this->runExample($child));
        }

        $this->eventDispatcher->dispatch('afterSpecification',
            new Event\SpecificationEvent($specification, microtime(true) - $startTime, $result)
        );

        if (null !== $oldHandler) {
            set_error_handler($oldHandler);
        }

        return $result;
    }

    public function runExample(Node\Example $example)
    {
        $context  = $example->getFunction()->getDeclaringClass()->newInstance();
        $prophets = new Prophet\CollaboratorsCollection;
        $matchers = new Matcher\MatchersCollection;

        foreach ($this->getInitializers() as $initializer) {
            if ($initializer->supports($context, $example)) {
                $initializer->initialize($context, $example, $prophets, $matchers);
            }
        }

        foreach ($this->getSubjectGuessers() as $guesser) {
            if ($guesser->supports($context)) {
                $context->setProphet(new Prophet\ObjectProphet(
                    new LazyObject($guesser->guess($context)), $matchers, $this->unwrapper
                ));

                break;
            }
        }

        $this->eventDispatcher->dispatch('beforeExample', new Event\ExampleEvent($example));
        $startTime = microtime(true);

        try {
            foreach ($example->getPreFunctions() as $preFunction) {
                $this->invoke($context, $preFunction, $prophets);
            }

            $this->invoke($context, $example->getFunction(), $prophets);

            foreach ($example->getPostFunctions() as $postFunction) {
                $this->invoke($context, $postFunction, $prophets);
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

        $event = new Event\ExampleEvent(
            $example, microtime(true) - $startTime, $status, $exception
        );
        $this->eventDispatcher->dispatch('afterExample', $event);

        return $event->getResult();
    }

    private function invoke($context, ReflectionFunctionAbstract $function,
                            Prophet\CollaboratorsCollection $prophets)
    {
        $parameters = array();
        foreach ($function->getParameters() as $parameter) {
            $parameters[] = $prophets->get($parameter->getName());
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
