<?php

namespace PHPSpec2\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionFunctionAbstract;
use ReflectionMethod;

use PHPSpec2\ObjectBehavior;

use PHPSpec2\Loader\Node\Specification;
use PHPSpec2\Loader\Node\Example;

use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;

use PHPSpec2\Exception\Example\ErrorException;
use PHPSpec2\Exception\Example\PendingException;
use PHPSpec2\Exception\Example\FailureException;

use PHPSpec2\Matcher\MatchersCollection;

use PHPSpec2\Mocker\MockerInterface;
use PHPSpec2\Mocker\MockBehavior;

use PHPSpec2\Prophet\ObjectProphet;
use PHPSpec2\Prophet\MockProphet;

use PHPSpec2\Subject\LazyObject;

use PHPSpec2\Wrapper\ArgumentsUnwrapper;

class Runner
{
    private $eventDispatcher;
    private $matchers;
    private $mocker;
    private $unwrapper;

    public function __construct(EventDispatcherInterface $dispatcher,
                                MatchersCollection $matchers, MockerInterface $mocker,
                                ArgumentsUnwrapper $unwrapper)
    {
        $this->eventDispatcher = $dispatcher;
        $this->matchers        = $matchers;
        $this->mocker          = $mocker;
        $this->unwrapper       = $unwrapper;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function runSpecification(Specification $specification)
    {
        if (defined('PHPSPEC_ERROR_REPORTING')) {
            $errorLevel = PHPSPEC_ERROR_REPORTING;
        } else {
            $errorLevel = E_ALL ^ E_WARNING;
        }
        $oldHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        $this->eventDispatcher->dispatch('beforeSpecification',
            new SpecificationEvent($specification)
        );
        $startTime = microtime(true);

        $result = ExampleEvent::PASSED;
        foreach ($specification->getChildren() as $child) {
            $result = max($result, $this->runExample($child));
        }

        $this->eventDispatcher->dispatch('afterSpecification',
            new SpecificationEvent($specification, microtime(true) - $startTime, $result)
        );

        if (null !== $oldHandler) {
            set_error_handler($oldHandler);
        }

        return $result;
    }

    public function runExample(Example $example)
    {
        $this->eventDispatcher->dispatch('beforeExample', new ExampleEvent($example));
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

            $status    = ExampleEvent::PASSED;
            $exception = null;
        } catch (PendingException $e) {
            $status    = ExampleEvent::PENDING;
            $exception = $e;
        } catch (FailureException $e) {
            $status    = ExampleEvent::FAILED;
            $exception = $e;
        } catch (\Exception $e) {
            $status    = ExampleEvent::BROKEN;
            $exception = $e;
        }

        $event = new ExampleEvent($example, microtime(true) - $startTime, $status, $exception);
        $this->eventDispatcher->dispatch('afterExample', $event);

        return $event->getResult();
    }

    protected function createContext(Example $example)
    {
        $function = $example->getFunction();
        $context  = $function->getDeclaringClass()->newInstance();

        $context->setProphet(new ObjectProphet(
            new LazyObject($example->getSubject()), $this->matchers, $this->unwrapper
        ));

        return $context;
    }

    protected function createMockBehavior($subject = null)
    {
        return new MockProphet($subject, $this->mocker, $this->unwrapper);
    }

    protected function getExampleDependencies(Example $example, $context)
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
                    $dependencies[$match[2]] = $this->createMockBehavior();
                    $dependencies[$match[2]]->beAMockOf($match[1]);
                }
            }
        }

        foreach ($function->getParameters() as $parameter) {
            if (!isset($dependencies[$parameter->getName()])) {
                $dependencies[$parameter->getName()] = $this->createMockBehavior();
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
                $parameters[] = $this->createMockBehavior();
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
            throw new ErrorException($level, $message, $file, $line);
        }

        // error reporting turned off or more likely suppressed with @
        return false;
    }
}
