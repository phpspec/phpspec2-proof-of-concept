<?php

namespace PHPSpec2;

use ReflectionMethod;
use ReflectionProperty;

use PHPSpec2\Matcher\MatchersCollection;
use PHPSpec2\Looper\Looper;

use PHPSpec2\Wrapper\LazyObject;
use PHPSpec2\Wrapper\LazySubjectInterface;
use PHPSpec2\Wrapper\ArgumentsResolver;
use PHPSpec2\Wrapper\SubjectWrapperInterface;

use PHPSpec2\Exception\BehaviorException;
use PHPSpec2\Exception\MethodNotFoundException;
use PHPSpec2\Exception\PropertyNotFoundException;

class ObjectBehavior implements SpecificationInterface, SubjectWrapperInterface
{
    private $subject;
    private $matchers;
    private $resolver;

    public function __construct($subject = null, MatchersCollection $matchers,
                                ArgumentsResolver $resolver)
    {
        $this->subject  = $subject;
        $this->matchers = $matchers;
        $this->resolver = $resolver;
    }

    public function objectIsAnInstanceOf($classname, array $constructorArguments = array())
    {
        if (!$this->subject instanceof LazySubjectInterface) {
            $this->subject = $this->createLazySubject();
        }

        if (!is_string($classname)) {
            throw new BehaviorException(sprintf(
                'Behavior subject classname should be string, <value>%s</value> given.',
                $this->representer->representValue($classname)
            ));
        }

        $this->subject->setClassname($classname);
        $this->subject->setConstructorArguments($this->resolver->resolveAll($constructorArguments));
    }

    public function objectIsConstructedWith()
    {
        if (null === $this->subject) {
            throw new BehaviorException(
                'You can not set object arguments. Behavior subject is null.'
            );
        }

        if (!$this->subject instanceof LazySubjectInterface) {
            throw new BehaviorException(
                'You can not set object arguments. Behavior subject is already initialized.'
            );
        }

        $this->subject->setConstructorArguments($this->resolver->resolveAll(func_get_args()));
    }

    public function should($name = null, array $arguments = array())
    {
        if (null === $name) {
            return new Looper(array($this, __METHOD__));
        }

        $subject   = $this->resolver->resolveSingle($this);
        $arguments = $this->resolver->resolveAll($arguments);
        $matcher   = $this->matchers->find($name, $subject, $arguments);

        return $matcher->positiveMatch($name, $subject, $arguments);
    }

    public function shouldNot($name = null, array $arguments = array())
    {
        if (null === $name) {
            return new Looper(array($this, __METHOD__));
        }

        $subject   = $this->resolver->resolveSingle($this);
        $arguments = $this->resolver->resolveAll($arguments);
        $matcher   = $this->matchers->find($name, $subject, $arguments);

        return $matcher->negativeMatch($name, $subject, $arguments);
    }

    public function callOnBehaviorSubject($method, array $arguments = array())
    {
        if (null === $this->getWrappedSubject()) {
            throw new BehaviorException(sprintf(
                'Call to a member function <value>%s()</value> on a non-object.',
                $method
            ));
        }

        // resolve arguments
        $arguments = $this->resolver->resolveAll($arguments);

        // if subject is an instance with provided method - call it and stub the result
        if ($this->isSubjectMethodAccessible($method)) {
            $returnValue = call_user_func_array(array($this->getWrappedSubject(), $method), $arguments);

            return new static($returnValue, $this->matchers, $this->resolver);
        }

        throw new MethodNotFoundException($this->getWrappedSubject(), $method);
    }

    public function setToBehaviorSubject($property, $value = null)
    {
        $value = $this->resolver->resolveAll($value);

        if ($this->isSubjectPropertyAccessible($property, true)) {
            return $this->getWrappedSubject()->$property = $value;
        }

        throw new PropertyNotFoundException($this->getWrappedSubject(), $property);
    }

    public function getFromBehaviorSubject($property)
    {
        if ($this->isSubjectPropertyAccessible($property)) {
            $returnValue = $this->getWrappedSubject()->$property;

            return new static($returnValue, $this->matchers, $this->resolver);
        }

        throw new PropertyNotFoundException($this->getWrappedSubject(), $property);
    }

    public function getWrappedSubject()
    {
        if (is_object($this->subject) && $this->subject instanceof LazySubjectInterface) {
            $this->subject = $this->subject->getInstance();
        }

        return $this->subject;
    }

    public function getBehaviorSubject()
    {
        return $this->subject;
    }

    public function getBehaviorMatchers()
    {
        return $this->matchers;
    }

    public function getBehaviorResolver()
    {
        return $this->resolver;
    }

    public function __call($method, array $arguments = array())
    {
        // if user calls function with should prefix - call matcher
        if (preg_match('/^(should(?:Not|))(.+)$/', $method, $matches)) {
            $matcherName = lcfirst($matches[2]);
            if ('should' === $matches[1]) {
                return $this->should($matcherName, $arguments);
            }

            return $this->shouldNot($matcherName, $arguments);
        }

        return $this->callOnBehaviorSubject($method, $arguments);
    }

    public function __set($property, $value = null)
    {
        return $this->setToBehaviorSubject($property, $value);
    }

    public function __get($property)
    {
        return $this->getFromBehaviorSubject($property);
    }

    protected function createLazySubject()
    {
        return new LazyObject;
    }

    private function isSubjectMethodAccessible($method)
    {
        if (!is_object($this->getWrappedSubject())) {
            return false;
        }

        if (method_exists($this->getWrappedSubject(), '__call')) {
            return true;
        }

        if (!method_exists($this->getWrappedSubject(), $method)) {
            return false;
        }

        $methodReflection = new ReflectionMethod($this->getWrappedSubject(), $method);

        return $methodReflection->isPublic();
    }

    private function isSubjectPropertyAccessible($property, $withValue = false)
    {
        if (!is_object($this->getWrappedSubject())) {
            return false;
        }

        if (method_exists($this->getWrappedSubject(), $withValue ? '__set' : '__get')) {
            return true;
        }

        if (!property_exists($this->getWrappedSubject(), $property)) {
            return false;
        }

        $propertyReflection = new ReflectionProperty($this->getWrappedSubject(), $property);

        return $propertyReflection->isPublic();
    }
}
