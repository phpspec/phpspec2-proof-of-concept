<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionClass;
use ReflectionMethod;

use Mockery;

use PHPSpec2\Stub\ObjectStub;
use PHPSpec2\Matcher\MatchersCollection;

use PHPSpec2\Event\SpecificationEvent;
use PHPSpec2\Event\ExampleEvent;

use PHPSpec2\Exception\Example\ErrorException;
use PHPSpec2\Exception\Example\PendingException;
use PHPSpec2\Stub\LazyInstance;

class Tester
{
    private $eventDispatcher;
    private $matchers = array();

    public function __construct(EventDispatcherInterface $dispatcher, MatchersCollection $matchers)
    {
        $this->eventDispatcher = $dispatcher;
        $this->matchers        = $matchers;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function testSpecification(ReflectionClass $spec)
    {
        $this->eventDispatcher->dispatch('beforeSpecification',
            new SpecificationEvent($spec)
        );

        $result = 0;
        foreach ($spec->getMethods(ReflectionMethod::IS_PUBLIC) as $example) {
            if ($this->isExampleTestable($example)) {
                $result = max($result, $this->testExample($example));
            }
        }

        $this->eventDispatcher->dispatch('afterSpecification',
            new SpecificationEvent($spec, $result)
        );

        return $result;
    }

    public function testExample(ReflectionMethod $example)
    {
        $this->eventDispatcher->dispatch('beforeExample', new ExampleEvent($example));

        $spec    = $example->getDeclaringClass();
        $subject = null;
        if (class_exists($class = preg_replace(array("|^spec\\\|", "|Spec$|"), '', $spec->getName()))) {
            $subject = new LazyInstance($class);
        }

        $instance = $spec->newInstance();
        $instance->object = new ObjectStub($subject, $this->matchers);
        $stubs = $this->getStubsForExample($instance, $example);

        if (defined('PHPSPEC_ERROR_REPORTING')) {
            $errorLevel = PHPSPEC_ERROR_REPORTING;
        } else {
            $errorLevel = E_ALL ^ E_WARNING;
        }
        $oldHandler = set_error_handler(array($this, 'errorHandler'), $errorLevel);

        try {
            $this->callMethodWithStubs($instance, $example, $stubs);
            Mockery::close();

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

    protected function getStubsForExample(Specification $instance, ReflectionMethod $example)
    {
        $stubs = array();
        if (method_exists($instance, 'described_with')) {
            $descriptor = new ReflectionMethod($instance, 'described_with');
            $stubs = $this->mergeStubsFromMethod($stubs, $descriptor);
            $this->callMethodWithStubs($instance, $descriptor, $stubs);
        }

        return $this->mergeStubsFromMethod($stubs, $example);
    }

    protected function callMethodWithStubs(Specification $instance, ReflectionMethod $method, array $stubs)
    {
        $arguments = array();
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $stubs[$parameter->getName()];
        }

        $method->invokeArgs($instance, $arguments);
    }

    private function mergeStubsFromMethod(array $stubs, ReflectionMethod $method)
    {
        foreach ($method->getParameters() as $parameter) {
            if (!isset($stubs[$parameter->getName()])) {
                $stubs[$parameter->getName()] = new ObjectStub(null, $this->matchers);
            }
        }

        return $stubs;
    }

    private function isExampleTestable(ReflectionMethod $example)
    {
        return 'described_with' !== $example->getName();
    }
}
