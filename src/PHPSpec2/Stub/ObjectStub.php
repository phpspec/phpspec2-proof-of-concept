<?php

namespace PHPSpec2\Stub;

use ReflectionClass;

use Mockery;
use Mockery\MockInterface;

use PHPSpec2\Matcher\MatcherInterface;

use PHPSpec2\Exception\Stub\StubException;
use PHPSpec2\Exception\Stub\ClassDoesNotExistsException;
use PHPSpec2\Exception\Stub\MatcherNotFoundException;
use PHPSpec2\Exception\Stub\MethodNotFoundException;
use PHPSpec2\Exception\Stub\PropertyNotFoundException;

class ObjectStub
{
    private $subject;
    private $matchers = array();

    public function __construct($subject = null, array $matchers = array())
    {
        $this->subject = $subject;
        $this->setStubMatchers($matchers);
    }

    public function is_an_instance_of($class, array $constructorArguments = array())
    {
        $this->isAnInstanceOf($class, $constructorArguments);
    }

    public function is_a_mock_of($classOrInterface)
    {
        $this->isAMockOf($classOrInterface);
    }

    public function isAnInstanceOf($class, array $constructorArguments = array())
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

    public function isAMockOf($classOrInterface)
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
            if (method_exists($this->subject, $method)) {
                $returnValue = call_user_func_array(array($this->subject, $method), $arguments);

                return new static($returnValue, $this->matchers);
            }
        }

        throw new MethodNotFoundException($method);
    }

    public function setToStub($property, $value = null)
    {
        $value = $this->resolveArgumentsStubs($value);

        if (property_exists($this->subject, $property)) {
            return $this->subject->$property = $value;
        }

        throw new PropertyNotFoundException($property);
    }

    public function getFromStub($property)
    {
        if (property_exists($this->subject, $property)) {
            return new static($this->subject->$property, $this->matchers);
        }

        throw new PropertyNotFoundException($property);
    }

    public function getStubSubject()
    {
        return $this->subject;
    }

    public function setStubMatchers(array $matchers = array())
    {
        $this->matchers = array();
        foreach ($matchers as $matcher) {
            $this->registerStubMatcher($matcher);
        }
    }

    public function registerStubMatcher(MatcherInterface $matcher)
    {
        foreach ($matcher->getAliases() as $alias) {
            $this->matchers[$alias] = $matcher;
        }
    }

    public function getStubMatchers()
    {
        return $this->matchers;
    }

    public function __call($method, array $arguments = array())
    {
        // if user calls matcher - find & run it or throw exception
        if (preg_match('/should[A-Z\_]/', $method)) {
            if (isset($this->matchers[$method])) {
                $arguments = $this->resolveArgumentsStubs($arguments);

                return $this->matchers[$method]->match($this, $method, $arguments);
            }

            throw new MatcherNotFoundException($method);
        }

        return $this->callOnStub($method, $arguments);
    }

    public function __set($property, $value = null)
    {
        return $this->setToStub($property, $value);
    }

    public function __get($property)
    {
        return $this->getFromStub($property);
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
}
