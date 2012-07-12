<?php

namespace PHPSpec2;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use ReflectionClass;
use ReflectionMethod;

use Mockery;

class Tester
{
    private static $descriptionMethods = array('describedWith', 'described_with');
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        $this->eventDispatcher = $dispatcher;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function testSpecification(ReflectionClass $spec)
    {
        foreach ($spec->getMethods(ReflectionMethod::IS_PUBLIC) as $example) {
            if ($this->isExampleTestable($example)) {
                $this->testExample($spec, $example);
            }
        }
    }

    public function testExample(ReflectionClass $spec, ReflectionMethod $example)
    {
        $instance = $spec->newInstance();
        $stubs    = $this->getStubsForExample($instance, $example);

        try {
            $this->callMethodWithStubs($instance, $example, $stubs);
            Mockery::close();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getStubsForExample(SpecificationInterface $instance, ReflectionMethod $example)
    {
        $stubs = array();
        foreach (self::$descriptionMethods as $name) {
            if (method_exists($instance, $name)) {
                $descriptor = new ReflectionMethod($instance, $name);
                $stubs = $this->mergeStubsFromMethod($stubs, $descriptor);
                $this->callMethodWithStubs($instance, $descriptor, $stubs);
            }
        }

        return $this->mergeStubsFromMethod($stubs, $example);
    }

    protected function callMethodWithStubs(SpecificationInterface $instance, ReflectionMethod $method, array $stubs)
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
                $stubs[$parameter->getName()] = $this->createNewStub();
            }
        }

        return $stubs;
    }

    private function createNewStub($subject = null)
    {
        $stub = new Stub($subject);
        $stub->registerStubMatcher(new Matcher\ShouldReturnMatcher);
        $stub->registerStubMatcher(new Matcher\ShouldContainMatcher);

        return $stub;
    }

    private function isExampleTestable(ReflectionMethod $example)
    {
        return !in_array($example->getName(), self::$descriptionMethods);
    }
}
