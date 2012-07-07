<?php

namespace PHPSpec\PHPSpec2;

use ReflectionClass;

use Mockery;
use Mockery\MockInterface;

use PHPSpec\PHPSpec2\Matcher\MatcherInterface;
use PHPSpec\PHPSpec2\Exception\StubException;
use PHPSpec\PHPSpec2\Exception\ClassDoesNotExistsException;
use PHPSpec\PHPSpec2\Exception\MethodNotFoundException;

class Stub
{
    private $subject;
    private $matchers = array();

    public function __construct($subject = null, array $matchers = array())
    {
        $this->subject = $subject;

        foreach ($matchers as $matcher) {
            $this->registerMatcher($matcher);
        }
    }

    public function registerMatcher(MatcherInterface $matcher)
    {
        foreach ($matcher->getAliases() as $alias) {
            $this->matchers[$alias] = $matcher;
        }
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
    }

    public function __call($method, array $arguments = array())
    {
        $method    = preg_replace('/^__/', '', $method);
        $arguments = $this->resolveArgumentsStubs($arguments);

        // if subject is a mock - generate method call expectation
        if ($this->subject instanceof MockInterface) {
            $expectation = $this->subject->shouldReceive($method);
            $expectation = call_user_func_array(array($expectation, 'with'), $arguments);
            $expectation->zeroOrMoreTimes();

            return new static($expectation, $this->matchers);
        }

        // if subject is an instance with provided method - call it and stub result
        if (method_exists($this->subject, $method)) {
            $returnValue = call_user_func_array(array($this->subject, $method), $arguments);

            return new static($returnValue, $this->matchers);
        }

        // if there is a registered matcher with specified alias - call test and return it
        if (isset($this->matchers[$method])) {
            return $this->matchers[$method]->match($this, $arguments);
        }

        throw new MethodNotFoundException($method);
    }

    public function __set($property, $value)
    {
        $value = $this->resolveArgumentsStubs($value);

        if (property_exists($this->subject, $property)) {
            return $this->subject->$property = $value;
        }

        throw new PropertyNotFoundException($property);
    }

    public function __get($property)
    {
        if (property_exists($this->subject, $property)) {
            return new static($this->subject->$property, $this->matchers);
        }

        throw new PropertyNotFoundException($property);
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getMatchers()
    {
        return $this->matchers;
    }

    protected function resolveArgumentsStubs($arguments)
    {
        return array_map(
            function($argument) {
                return $argument instanceof Stub ? $argument->getSubject() : $argument;
            },
            (array) $arguments
        );
    }
}
