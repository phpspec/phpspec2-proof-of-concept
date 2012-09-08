<?php

namespace PHPSpec2\Stub;

use ReflectionMethod;
use ReflectionProperty;

use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Mocker\MockerFactory;
use PHPSpec2\Mocker\MockProxyInterface;

use PHPSpec2\Exception\Stub\StubException;
use PHPSpec2\Exception\Stub\MethodNotFoundException;
use PHPSpec2\Exception\Stub\PropertyNotFoundException;

class ObjectStub
{
    private $subject;
    private $matchers;
    private $mockers;
    private $resolver;

    public function __construct($subject = null, MatchersCollection $matchers,
                                MockerFactory $mockers = null, ArgumentsResolver $resolver = null)
    {
        $this->subject  = $subject;
        $this->matchers = $matchers;
        $this->mockers  = $mockers  ?: new MockerFactory();
        $this->resolver = $resolver ?: new ArgumentsResolver();
    }

    public function isAnInstanceOf($class, array $constructorArguments = array())
    {
        $this->subject = new LazyInstance($class, $this->resolver->resolve($constructorArguments));
    }

    public function isAMockOf($classOrInterface)
    {
        $this->subject = new LazyMock($classOrInterface, $this->mockers);
    }

    public function should()
    {
        return new Verification\Positive($this->getStubSubject(), $this->matchers, $this->resolver);
    }

    public function shouldNot()
    {
        return new Verification\Negative($this->getStubSubject(), $this->matchers, $this->resolver);
    }

    public function callOnStub($method, array $arguments = array())
    {
        if (null === $this->getStubSubject()) {
            throw new StubException('Attempt to call method on stub without a subject');
        }

        // resolve arguments
        $arguments = $this->resolver->resolve($arguments);

        // if subject is an instance with provided method - call it and stub the result
        if ($this->isSubjectMethodAccessible($method)) {
            $returnValue = call_user_func_array(array($this->getStubSubject(), $method), $arguments);

            return new static($returnValue, $this->matchers, $this->mockers, $this->resolver);
        }

        // if subject is a mock - return method expectation stub
        if ($this->getStubSubject() instanceof MockProxyInterface) {
            return $this->getStubSubject()->mockMethod($method, $arguments, $this->resolver);
        }

        if ($this->subjectHasAMagicCall()) {
            return $this->invokeSubjectMagicCall($method, $arguments);
        }

        throw new MethodNotFoundException($this->getStubSubject(), $method);
    }

    public function setToStub($property, $value = null)
    {
        $value = $this->resolver->resolve($value);

        if ($this->isSubjectPropertyAccessible($property)) {
            return $this->getStubSubject()->$property = $value;
        }

        throw new PropertyNotFoundException($property);
    }

    public function getFromStub($property)
    {
        if ($this->isSubjectPropertyAccessible($property)) {
            return new static($this->getStubSubject()->$property, $this->matchers);
        }

        throw new PropertyNotFoundException($property);
    }

    public function getStubSubject()
    {
        if (is_object($this->subject) && $this->subject instanceof LazySubjectInterface) {
            $this->subject = $this->subject->getInstance();
        }

        return $this->subject;
    }

    public function __call($method, array $arguments = array())
    {
        // if user calls function with should prefix - call matcher
        if (preg_match('/^(should(?:Not|))(.+)$/', $method, $matches)) {
            $matcherName = lcfirst($matches[2]);
            if ('should' === $matches[1]) {
                return call_user_func_array(array($this->should(), $matcherName), $arguments);
            } else {
                return call_user_func_array(array($this->shouldNot(), $matcherName), $arguments);
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

    private function isSubjectMethodAccessible($method)
    {
        if (!is_object($this->getStubSubject()) || !method_exists($this->getStubSubject(), $method)) {
            return false;
        }

        $methodReflection = new ReflectionMethod($this->getStubSubject(), $method);

        return $methodReflection->isPublic();
    }

    private function isSubjectPropertyAccessible($property)
    {
        if (!is_object($this->getStubSubject()) || !property_exists($this->getStubSubject(), $property)) {
            return false;
        }

        $propertyReflection = new ReflectionProperty($this->getStubSubject(), $property);

        return $propertyReflection->isPublic();
    }

    private function subjectHasAMagicCall()
    {
        return method_exists($this->getStubSubject(), '__call');
    }

    private function invokeSubjectMagicCall($method, $arguments)
    {
        return new static(
            $this->getStubSubject()->__call($method, $arguments),
            $this->matchers
        );
    }
}
