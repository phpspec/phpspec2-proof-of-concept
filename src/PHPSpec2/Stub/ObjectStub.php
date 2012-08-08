<?php

namespace PHPSpec2\Stub;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Mocker\MockerFactory;
use PHPSpec2\Mocker\MockProxyInterface;

use PHPSpec2\Exception\Stub\StubException;
use PHPSpec2\Exception\Stub\ClassDoesNotExistsException;
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

    public function is_an_instance_of($class, array $constructorArguments = array())
    {
        $constructorArguments = $this->resolver->resolve($constructorArguments);

        if (!is_string($class)) {
            throw new StubException(sprintf(
                'Instantiator expects class name, "%s" got', gettype($class)
            ));
        }

        if (!class_exists($class)) {
            throw new ClassDoesNotExistsException($class);
        }

        $reflection = new ReflectionClass($class);

        $this->subject = new LazySubject($reflection, $constructorArguments);
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

        $this->subject = $this->mockers->mock($classOrInterface);
    }

    public function should()
    {
        return new Verification\Positive($this->resolveSubject(), $this->matchers, $this->resolver);
    }

    public function should_not()
    {
        return new Verification\Negative($this->resolveSubject(), $this->matchers, $this->resolver);
    }

    public function callOnStub($method, array $arguments = array())
    {
        if (null === $this->getSubject()) {
            throw new StubException('Attempt to call method on stub without a subject');
        }

        // resolve arguments
        $arguments = $this->resolver->resolve($arguments);

        // if subject is an instance with provided method - call it and stub the result
        if ($this->isSubjectMethodAccessible($method)) {
            $returnValue = call_user_func_array(array($this->getSubject(), $method), $arguments);

            return new static($returnValue, $this->matchers);
        }

        // if subject is a mock - return method expectation stub
        if ($this->getSubject() instanceof MockProxyInterface) {
            return $this->getSubject()->mockMethod($method, $arguments, $this->resolver);
        }

        throw new MethodNotFoundException($method);
    }

    public function setToStub($property, $value = null)
    {
        $value = $this->resolver->resolve($value);

        if ($this->isSubjectPropertyAccessible($property)) {
            return $this->resolveSubject()->$property = $value;
        }

        throw new PropertyNotFoundException($property);
    }

    public function getFromStub($property)
    {
        if ($this->isSubjectPropertyAccessible($property)) {
            return new static($this->resolveSubject()->$property, $this->matchers);
        }

        throw new PropertyNotFoundException($property);
    }

    public function getStubSubject()
    {
        return $this->resolveSubject();
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

    private function resolveSubject()
    {
        return $this->resolver->resolveSingle($this->getSubject());
    }

    private function getSubject()
    {
        return $this->subject = (
            $this->subject instanceof LazySubject ? $this->subject->instantiate() : $this->subject
        );
    }

    private function isSubjectMethodAccessible($method)
    {
        if (!method_exists($this->getSubject(), $method)) {
            return false;
        }

        $methodReflection = new ReflectionMethod($this->getSubject(), $method);

        return $methodReflection->isPublic();
    }

    private function isSubjectPropertyAccessible($property)
    {
        if (!property_exists($this->getSubject(), $property)) {
            return false;
        }

        $propertyReflection = new ReflectionProperty($this->getSubject(), $property);

        return $propertyReflection->isPublic();
    }
}
