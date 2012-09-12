<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionFunctionAbstract;
use ReflectionMethod;

use PHPSpec2\Prophet\Prophet;
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

        $result = ExampleEvent::PASSED;
        foreach ($specification->getChildren() as $child) {
            $result = max($result, $this->runExample($child));
        }

        $this->eventDispatcher->dispatch('afterSpecification',
            new SpecificationEvent($specification, $result)
        );

        return $result;
    }

    public function runExample(Example $example)
    {
        $this->eventDispatcher->dispatch('beforeExample', new ExampleEvent($example));

        $subject = new LazyObject($example->getSubject());
        $context = $example->getFunction()->getDeclaringClass()->newInstance();
        $ivar    = lcfirst(basename(str_replace('\\', '/', $example->getSubject())));
        $prophet = $this->createProphet($subject);

        $context->$ivar  = $context->object = $prophet;

        if (defined('PHPSPEC_ERROR_REPORTING')) {
            $errorLevel = PHPSPEC_ERROR_REPORTING;
        } else {
            $errorLevel = E_ALL ^ E_WARNING;
        }
        $oldHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        try {
            $dependencies = $this->getExampleDependencies($example, $context);
            $this->invokeWithArguments($context, $example->getFunction(), $dependencies);
            $this->mocker->teardown();

            $event = new ExampleEvent($example, ExampleEvent::PASSED);
        } catch (PendingException $e) {
            $event = new ExampleEvent($example, ExampleEvent::PENDING, $e);
        } catch (\Exception $e) {
            $event = new ExampleEvent($example, ExampleEvent::FAILED, $e);
        }

        if (null !== $oldHandler) {
            set_error_handler($oldHandler);
        }

        $this->eventDispatcher->dispatch('afterExample', $event);

        return $event->getResult();
    }

    private function getExampleDependencies(Example $example, $context)
    {
        $dependencies = array();

        foreach ($example->getPreFunctions() as $preFunction) {
            foreach ($this->getMethodDependencies($preFunction) as $name => $dependency) {
                if (!isset($dependencies[$name])) {
                    $dependencies[$name] = $dependency;
                }
            }
            $this->invokeWithArguments($context, $preFunction, $dependencies);
        }

        foreach ($this->getMethodDependencies($example->getFunction()) as $name => $dependency) {
            if (!isset($dependencies[$name])) {
                $dependencies[$name] = $dependency;
            }
        }

        return $dependencies;
    }

    private function getMethodDependencies(ReflectionFunctionAbstract $function)
    {
        $dependencies = array();

        foreach (explode("\n", trim($function->getDocComment())) as $line) {
            $line = preg_replace('/^\/\*\*\s*|^\s*\*\s*|\s*\*\/$|\s*$/', '', $line);

            if (preg_match('#^@param(?: *[^ ]*)? *\$([^ ]*) *(double|mock|stub|fake|dummy|spy) of (.*)$#', $line, $match)) {
                $dependencies[$match[1]] = $this->createProphet();
                $dependencies[$match[1]]->isAMockOf($match[3]);
            }
        }

        foreach ($function->getParameters() as $parameter) {
            if (!isset($dependencies[$parameter->getName()])) {
                $dependencies[$parameter->getName()] = $this->createProphet();
            }
        }

        return $dependencies;
    }

    private function invokeWithArguments($context, ReflectionMethod $method, array $arguments)
    {
        $parameters = array();
        foreach ($method->getParameters() as $parameter) {
            $parameters[] = $arguments[$parameter->getName()];
        }

        $method->invokeArgs($context, $parameters);
    }

    private function createProphet($subject = null)
    {
        return new Prophet($subject, clone $this->matchers, $this->mocker, $this->resolver);
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
