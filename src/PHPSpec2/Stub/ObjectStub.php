<?php

namespace PHPSpec2\Stub;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

use Mockery;
use Mockery\MockInterface;

use PHPSpec2\Matcher\MatchersCollection;

use PHPSpec2\Exception\Stub\StubException;
use PHPSpec2\Exception\Stub\ClassDoesNotExistsException;
use PHPSpec2\Exception\Stub\MethodNotFoundException;
use PHPSpec2\Exception\Stub\PropertyNotFoundException;

class ObjectStub
{
    private $subject;
    private $matchers;

    public function __construct($subject = null, MatchersCollection $matchers)
    {
        $this->subject  = $subject;
        $this->matchers = $matchers;
    }

    public function is_an_instance_of($class, array $constructorArguments = array())
    {
        $constructorArguments = $this->resolveArgumentsStubs($constructorArguments);

        if (!is_string($class)) {
            throw new StubException(sprintf(
                'Instantiator expects class name, "%s" got', gettype($class)
            ));
        }

        if (!class_exists($class)) {
            throw new ClassDoesNotExistsException($class);
        }

        $reflection = new ReflectionClass($class);

        $this->subject = $reflection->newInstanceArgs($constructorArguments);
    }

    public function is_a_mock_of($classOrInterface)
    {
        if (!is_string($classOrInterface)) {
            throw new StubException(sprintf(
                'Mock creator expects class or interface name, "%s" got',
                gettype($classOrInterface)
            ));
        }

        if (!class_exists($classOrInterface) && !interface_exists($classOrInterface)) {
            throw new ClassDoesNotExistsException($classOrInterface);
        }

        $this->subject = Mockery::mock($classOrInterface);
        $this->subject->shouldIgnoreMissing();
    }

    public function should()
    {
        return new Verification($this->subject, $this->matchers, true);
    }

    public function should_not()
    {
        return new Verification($this->subject, $this->matchers, false);
    }

    public function callOnStub($method, array $arguments = array())
    {
        $arguments = $this->resolveArgumentsStubs($arguments);

        // if there is a subject
        if (null !== $this->subject) {
            // if subject is a mock - return method expectation stub
            if ($this->subject instanceof MockInterface) {
                return new MethodExpectationStub(
                    $this->subject->shouldReceive($method),
                    $arguments
                );
            }

            // if subject is an instance with provided method - call it and stub the result
            if ($this->isSubjectMethodAccessible($method)) {
                $returnValue = call_user_func_array(array($this->subject, $method), $arguments);

                return new static($returnValue, $this->matchers);
            }
        }

        throw new MethodNotFoundException($method);
    }

    public function setToStub($property, $value = null)
    {
        $value = $this->resolveArgumentsStubs($value);

        if ($this->isSubjectPropertyAccessible($property)) {
            return $this->subject->$property = $value;
        }

        throw new PropertyNotFoundException($property);
    }

    public function getFromStub($property)
    {
        if ($this->isSubjectPropertyAccessible($property)) {
            return new static($this->subject->$property, $this->matchers);
        }

        throw new PropertyNotFoundException($property);
    }

    public function getStubSubject()
    {
        return $this->subject;
    }

    public function __call($method, array $arguments = array())
    {
        // if user calls function with should_ prefix - call matcher
        if (preg_match('/^(should(?:_not|)?)_(.+)$/', $method, $matches)) {
            $matcherName = $matches[2];
            if ('should' === $matches[1]) {
                return call_user_func_array(array($this->should(), $matcherName), $arguments);
            } else {
                return call_user_func_array(array($this->should_not(), $matcherName), $arguments);
            }
        }

        return $this->callOnStub($method, $arguments);
    }

    public function __get($property)
    {
        if (!$this->isSubjectPropertyAccessible($property)) {
            foreach (array('get', 'is') as $prefix) {
                $getter = sprintf('%s%s', $prefix, ucfirst($property));
                if ($this->isSubjectMethodAccessible($getter)) {
                    return $this->callOnStub($getter);
                }
            }
        }

        return $this->getFromStub($property);
    }

    public function __set($property, $value = null)
    {
        return $this->setToStub($property, $value);
    }

    protected function resolveArgumentsStubs($arguments)
    {
        if (null === $arguments) {
            return array();
        }

        return array_map(
            function($argument) {
                return $argument instanceof ObjectStub ? $argument->getStubSubject() : $argument;
            },
            (array) $arguments
        );
    }

    private function isSubjectMethodAccessible($method)
    {
        if (!method_exists($this->subject, $method)) {
            return false;
        }

        $methodReflection = new ReflectionMethod($this->subject, $method);

        return $methodReflection->isPublic();
    }

    private function isSubjectPropertyAccessible($property)
    {
        if (!property_exists($this->subject, $property)) {
            return false;
        }

        $propertyReflection = new ReflectionProperty($this->subject, $property);

        return $propertyReflection->isPublic();
    }
}
