<?php

namespace PHPSpec2\Prophet;

use ReflectionMethod;
use ReflectionProperty;

use PHPSpec2\Matcher\MatchersCollection;

use PHPSpec2\Looper\Looper;

use PHPSpec2\Wrapper\ArgumentsUnwrapper;

use PHPSpec2\Subject\LazySubjectInterface;
use PHPSpec2\Subject\LazyObject;

use PHPSpec2\Exception\BehaviorException;
use PHPSpec2\Exception\MethodNotFoundException;
use PHPSpec2\Exception\PropertyNotFoundException;
use PHPSpec2\Exception\InterfaceNotImplementedException;

use PHPSpec2\Formatter\Presenter\PresenterInterface;

use ArrayAccess;

class ObjectProphet implements ArrayAccess, ProphetInterface
{
    private $subject;
    private $matchers;
    private $unwrapper;
    private $presenter;

    public function __construct($subject = null, MatchersCollection $matchers,
                                ArgumentsUnwrapper $unwrapper, PresenterInterface $presenter)
    {
        $this->subject   = $subject;
        $this->matchers  = $matchers;
        $this->unwrapper = $unwrapper;
        $this->presenter = $presenter;
    }

    public function beAnInstanceOf($classname, array $constructorArguments = array())
    {
        if (!$this->subject instanceof LazySubjectInterface) {
            $this->subject = $this->createLazySubject();
        }

        if (!is_string($classname)) {
            throw new BehaviorException(sprintf(
                'Behavior subject classname should be string, %s given.',
                $this->presenter->presentValue($classname)
            ));
        }

        $this->subject->setClassname($classname);
        $this->subject->setConstructorArguments($this->unwrapper->unwrapAll($constructorArguments));
    }

    public function beConstructedWith()
    {
        if (null === $this->subject) {
            throw new BehaviorException(sprintf(
                'You can not set object arguments. Behavior subject is %s.',
                $this->presenter->presentValue(null)
            ));
        }

        if (!$this->subject instanceof LazySubjectInterface) {
            throw new BehaviorException(
                'You can not set object arguments. Behavior subject is already initialized.'
            );
        }

        $this->subject->setConstructorArguments($this->unwrapper->unwrapAll(func_get_args()));
    }

    public function should($name = null, array $arguments = array())
    {
        if (null === $name) {
            return new Looper(array($this, __METHOD__));
        }

        $subject   = $this->unwrapper->unwrapOne($this);
        $arguments = $this->unwrapper->unwrapAll($arguments);
        $matcher   = $this->matchers->find($name, $subject, $arguments);

        return $matcher->positiveMatch($name, $subject, $arguments);
    }

    public function shouldNot($name = null, array $arguments = array())
    {
        if (null === $name) {
            return new Looper(array($this, __METHOD__));
        }

        $subject   = $this->unwrapper->unwrapOne($this);
        $arguments = $this->unwrapper->unwrapAll($arguments);
        $matcher   = $this->matchers->find($name, $subject, $arguments);

        return $matcher->negativeMatch($name, $subject, $arguments);
    }

    public function callOnProphetSubject($method, array $arguments = array())
    {
        if (null === $this->getWrappedSubject()) {
            throw new BehaviorException(sprintf(
                'Call to a member function %s on a non-object.',
                $this->presenter->presentString($method.'()')
            ));
        }

        // resolve arguments
        $subject   = $this->unwrapper->unwrapOne($this);
        $arguments = $this->unwrapper->unwrapAll($arguments);

        // if subject is an instance with provided method - call it and stub the result
        if ($this->isSubjectMethodAccessible($method)) {
            $returnValue = call_user_func_array(array($subject, $method), $arguments);

            return new static($returnValue, $this->matchers, $this->unwrapper, $this->presenter);
        }

        throw new MethodNotFoundException(sprintf(
            'Method %s not found.',
            $this->presenter->presentString(get_class($subject).'::'.$method.'()')
        ), $subject, $method, $arguments);
    }

    public function setToProphetSubject($property, $value = null)
    {
        if (null === $this->getWrappedSubject()) {
            throw new BehaviorException(sprintf(
                'Setting property %s on a non-object.',
                $this->presenter->presentString($property)
            ));
        }

        $value = $this->unwrapper->unwrapAll($value);

        if ($this->isSubjectPropertyAccessible($property, true)) {
            return $this->getWrappedSubject()->$property = $value;
        }

        throw new PropertyNotFoundException(sprintf(
            'Property %s not found.',
            $this->presenter->presentString(get_class($this->getWrappedSubject()).'::'.$property)
        ), $this->getWrappedSubject(), $property);
    }

    public function getFromProphetSubject($property)
    {
        // transform camel-cased properties to constant lookups
        if (null !== $this->subject && $property === strtoupper($property)) {
            $class = get_class($this->subject);
            if ($this->subject instanceof LazyObject) {
                $class = $this->subject->getClassname();
            }
            if (defined($class.'::'.$property)) {
                return constant($class.'::'.$property);
            }
        }

        if (null === $this->getWrappedSubject()) {
            throw new BehaviorException(sprintf(
                'Getting property %s from a non-object.',
                $this->presentString($property)
            ));
        }

        if ($this->isSubjectPropertyAccessible($property)) {
            $returnValue = $this->getWrappedSubject()->$property;

            return new static($returnValue, $this->matchers, $this->unwrapper, $this->presenter);
        }

        throw new PropertyNotFoundException(sprintf(
            'Property %s not found.',
            $this->presenter->presentString(get_class($this->getWrappedSubject()).'::'.$property)
        ), $this->getWrappedSubject(), $property);
    }

    public function getWrappedSubject()
    {
        if (is_object($this->subject) && $this->subject instanceof LazySubjectInterface) {
            $this->subject = $this->subject->getInstance();
        }

        return $this->subject;
    }

    public function offsetExists($key)
    {
        $subject = $this->getWrappedSubject();
        $key     = $this->unwrapper->unwrapOne($key);

        if (is_object($subject) && !($subject instanceof ArrayAccess)) {
            throw new InterfaceNotImplementedException(
                sprintf('%s does not implement %s interface, but should.',
                    $this->presenter->presentValue($this->getWrappedSubject()),
                    $this->presenter->presentString('ArrayAccess')
                ),
                $this->getWrappedSubject(),
                'ArrayAccess'
            );
        } elseif (!($subject instanceof ArrayAccess) && !is_array($subject)) {
            throw new BehaviorException(sprintf(
                'Can not use %s as array.', $this->presenter->presentValue($subject)
            ));
        }

        return isset($subject[$key]);
    }

    public function offsetGet($key)
    {
        $subject = $this->getWrappedSubject();
        $key     = $this->unwrapper->unwrapOne($key);

        if (is_object($subject) && !($subject instanceof ArrayAccess)) {
            throw new InterfaceNotImplementedException(
                sprintf('%s does not implement %s interface, but should.',
                    $this->presenter->presentValue($this->getWrappedSubject()),
                    $this->presenter->presentString('ArrayAccess')
                ),
                $this->getWrappedSubject(),
                'ArrayAccess'
            );
        } elseif (!($subject instanceof ArrayAccess) && !is_array($subject)) {
            throw new BehaviorException(sprintf(
                'Can not use %s as array.', $this->presenter->presentValue($subject)
            ));
        }
        return new static($subject[$key], $this->matchers, $this->unwrapper, $this->presenter);
    }

    public function offsetSet($key, $value)
    {
        $subject = $this->getWrappedSubject();
        $key     = $this->unwrapper->unwrapOne($key);

        if (is_object($subject) && !($subject instanceof ArrayAccess)) {
            throw new InterfaceNotImplementedException(
                sprintf('%s does not implement %s interface, but should.',
                    $this->presenter->presentValue($this->getWrappedSubject()),
                    $this->presenter->presentString('ArrayAccess')
                ),
                $this->getWrappedSubject(),
                'ArrayAccess'
            );
        } elseif (!($subject instanceof ArrayAccess) && !is_array($subject)) {
            throw new BehaviorException(sprintf(
                'Can not use %s as array.', $this->presenter->presentValue($subject)
            ));
        }

        $subject[$key] = $value;
    }

    public function offsetUnset($key)
    {
        $subject = $this->getWrappedSubject();
        $key     = $this->unwrapper->unwrapOne($key);

        if (is_object($subject) && !($subject instanceof ArrayAccess)) {
            throw new InterfaceNotImplementedException(
                sprintf('%s does not implement %s interface, but should.',
                    $this->presenter->presentValue($this->getWrappedSubject()),
                    $this->presenter->presentString('ArrayAccess')
                ),
                $this->getWrappedSubject(),
                'ArrayAccess'
            );
        } elseif (!($subject instanceof ArrayAccess) && !is_array($subject)) {
            throw new BehaviorException(sprintf(
                'Can not use %s as array.', $this->presenter->presentValue($subject)
            ));
        }

        unset($subject[$key]);
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

        return $this->callOnProphetSubject($method, $arguments);
    }

    public function __invoke()
    {
        return $this->callOnProphetSubject('__invoke', func_get_args());
    }

    public function __set($property, $value = null)
    {
        return $this->setToProphetSubject($property, $value);
    }

    public function __get($property)
    {
        return $this->getFromProphetSubject($property);
    }

    protected function createLazySubject()
    {
        return new LazyObject(null, array(), $this->presenter);
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
