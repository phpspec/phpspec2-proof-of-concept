<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionFunctionAbstract;
use ReflectionMethod;

use PHPSpec2\Prophet\ObjectProphet;
use PHPSpec2\Prophet\MockProphet;
use PHPSpec2\Prophet\LazyObject;
use PHPSpec2\Prophet\ArgumentsResolver;

use PHPSpec2\Loader\Node\Specification;
use PHPSpec2\Loader\Node\Example;

use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;

use PHPSpec2\Exception\Example\ErrorException;
use PHPSpec2\Exception\Example\PendingException;

use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Mocker\MockerInterface;

class Runner
{
    private $eventDispatcher;
    private $matchers;
    private $mocker;
    private $resolver;

    public function __construct(EventDispatcherInterface $dispatcher,
                                MatchersCollection $matchers, MockerInterface $mocker,
                                ArgumentsResolver $resolver)
    {
        $this->eventDispatcher = $dispatcher;
        $this->matchers        = $matchers;
        $this->mocker          = $mocker;
        $this->resolver        = $resolver;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function runSpecification(Specification $specification)
    {
        $this->eventDispatcher->dispatch('beforeSpecification',
            new SpecificationEvent($specification)
        );
        $startTime = microtime(true);

        $result = ExampleEvent::PASSED;
        foreach ($specification->getChildren() as $child) {
            if ($child instanceof Specification) {
                $result = max($result, $this->runSpecification($child));
            } else {
                $result = max($result, $this->runExample($child));
            }
        }

        $this->eventDispatcher->dispatch('afterSpecification',
            new SpecificationEvent($specification, microtime(true) - $startTime, $result)
        );

        return $result;
    }

    public function runExample(Example $example)
    {
        $this->eventDispatcher->dispatch('beforeExample', new ExampleEvent($example));
        $startTime = microtime(true);

        $subject = null;
        $varName = 'thing';
        if (null !== $subjectName = $example->getSubject()) {
            $subject = new LazyObject($subjectName);
            $varName = lcfirst(basename(str_replace('\\', '/', $subjectName)));
        }

        $prophet = $this->createObjectProphet($subject);
        $context = $this->createContext($example);
        $context->$varName = $context->object = $prophet;

        if (defined('PHPSPEC_ERROR_REPORTING')) {
            $errorLevel = PHPSPEC_ERROR_REPORTING;
        } else {
            $errorLevel = E_ALL ^ E_WARNING;
        }
        $oldHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        try {
            $dependencies = $this->getExampleDependencies($example, $context);
            $this->invoke($context, $example->getFunction(), $dependencies);
            $this->mocker->verify();
            foreach ($example->getPostFunctions() as $postFunction) {
                $this->invoke($context, $postFunction, $dependencies);
            }

            $event = new ExampleEvent(
                $example, microtime(true) - $startTime, ExampleEvent::PASSED
            );
        } catch (PendingException $e) {
            $event = new ExampleEvent(
                $example, microtime(true) - $startTime, ExampleEvent::PENDING, $e
            );
        } catch (\Exception $e) {
            $event = new ExampleEvent(
                $example, microtime(true) - $startTime, ExampleEvent::FAILED, $e
            );
        }

        if (null !== $oldHandler) {
            set_error_handler($oldHandler);
        }

        $this->eventDispatcher->dispatch('afterExample', $event);

        return $event->getResult();
    }

    protected function createContext(Example $example)
    {
        $function = $example->getFunction();

        if ($function instanceof ReflectionMethod) {
            return $function->getDeclaringClass()->newInstance();
        } else {
            return new \stdClass;
        }
    }

    protected function createObjectProphet($subject = null)
    {
        return new ObjectProphet($subject, clone $this->matchers, $this->resolver);
    }

    protected function createMockProphet($subject = null)
    {
        return new MockProphet($subject, $this->mocker, $this->resolver);
    }

    protected function getExampleDependencies(Example $example, $context)
    {
        $dependencies = array();

        foreach ($example->getPreFunctions() as $preFunction) {
            $dependencies = $this->getDependencies($preFunction, $dependencies);
            $this->invoke($context, $preFunction, $dependencies);
        }

        return $this->getDependencies($example->getFunction(), $dependencies);
    }

    private function getDependencies(ReflectionFunctionAbstract $function, array $dependencies)
    {
        foreach (explode("\n", trim($function->getDocComment())) as $line) {
            $line = preg_replace('/^\/\*\*\s*|^\s*\*\s*|\s*\*\/$|\s*$/', '', $line);

            if (preg_match('#^@param(?: *[^ ]*)? *\$([^ ]*) *(double|mock|stub|fake|dummy|spy) of (.*)$#', $line, $match)) {
                $dependencies[$match[1]] = $this->createMockProphet();
                $dependencies[$match[1]]->isAMockOf($match[3]);
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

        if ($function instanceof ReflectionMethod) {
            $function->invokeArgs($context, $parameters);
        } elseif ($function->isClosure()) {
            $closure = $function->getClosure()->bindTo($context);
            call_user_func_array($closure, $parameters);
        }
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
