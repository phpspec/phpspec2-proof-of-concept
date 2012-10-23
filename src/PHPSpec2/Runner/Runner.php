<?php

namespace PHPSpec2\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionFunctionAbstract;
use ReflectionMethod;

use PHPSpec2\ObjectBehavior;
use PHPSpec2\Loader\Node;
use PHPSpec2\Event;
use PHPSpec2\Exception\Example as ExampleException;
use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Prophet;
use PHPSpec2\Subject\LazyObject;
use PHPSpec2\Wrapper\ArgumentsUnwrapper;
use PHPSpec2\Initializer\InitializerInterface;
use PHPSpec2\Prophet\SubjectGuesserInterface;

class Runner
{
    private $eventDispatcher;
    private $matchers;
    private $mocker;
    private $unwrapper;

    private $guessers = array();
    private $guessersSorted = false;
    private $initializers = array();
    private $initializersSorted = false;

    public function __construct(EventDispatcherInterface $dispatcher,
                                MatchersCollection $matchers, MockerInterface $mocker,
                                ArgumentsUnwrapper $unwrapper)
    {
        $this->eventDispatcher = $dispatcher;
        $this->matchers        = $matchers;
        $this->mocker          = $mocker;
        $this->unwrapper       = $unwrapper;
    }

    public function registerInitializer(InitializerInterface $initializer)
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

    public function registerSubjectGuesser(SubjectGuesserInterface $guesser)
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
        $this->eventDispatcher->dispatch('beforeExample', new Event\ExampleEvent($example));
        $startTime = microtime(true);

        $context = $this->createContext($example);
        $dependencies = $this->getExampleDependencies($example, $context);

        try {
            foreach ($example->getPreFunctions() as $preFunction) {
                $this->invoke($context, $preFunction, $dependencies);
            }

            $this->invoke($context, $example->getFunction(), $dependencies);

            foreach ($example->getPostFunctions() as $postFunction) {
                $this->invoke($context, $postFunction, $dependencies);
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

    protected function createContext(Node\Example $example)
    {
        $function = $example->getFunction();
        $context  = $function->getDeclaringClass()->newInstance();

        $context->setProphet(new Prophet\ObjectProphet(
            new LazyObject($example->getSubject()), $this->matchers, $this->unwrapper
        ));

        return $context;
    }

    protected function createMockProphet($subject = null)
    {
        return new Prophet\MockProphet($subject, $this->mocker, $this->unwrapper);
    }

    protected function getExampleDependencies(Node\Example $example, $context)
    {
        $dependencies = array();
        foreach ($example->getPreFunctions() as $preFunction) {
            $dependencies = $this->getDependencies($preFunction, $dependencies);
        }

        return $this->getDependencies($example->getFunction(), $dependencies);
    }

    private function getDependencies(ReflectionFunctionAbstract $function, array $dependencies)
    {
        foreach (explode("\n", trim($function->getDocComment())) as $line) {
            if (preg_match('#@param *([^ ]*) *\$([^ ]*)#', $line, $match)) {
                if (!isset($dependencies[$match[2]])) {
                    $dependencies[$match[2]] = $this->createMockProphet();
                    $dependencies[$match[2]]->beAMockOf($match[1]);
                }
            }
        }

        foreach ($function->getParameters() as $parameter) {
            if (!isset($dependencies[$parameter->getName()])) {
                $dependencies[$parameter->getName()] = $this->createMockProphet();
            }
        }

        return $dependencies;
    }

    private function invoke($context, ReflectionFunctionAbstract $function, array $dependencies)
    {
        $parameters = array();
        foreach ($function->getParameters() as $parameter) {
            if (isset($dependencies[$parameter->getName()])) {
                $parameters[] = $dependencies[$parameter->getName()];
            } else {
                $parameters[] = $this->createMockProphet();
            }
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
