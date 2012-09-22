<?php

namespace PHPSpec2;

use PHPSpec2\Wrapper\LazyMethod;
use PHPSpec2\Exception\Prophet\ProphetException;

class MethodBehavior extends ObjectBehavior
{
    public function objectIsAnInstanceOf($classname, array $constructorArguments = array())
    {
        if (!$this->getBehaviorSubject() instanceof LazyMethod) {
            $this->setBehaviorSubject(new LazyMethod);
        }

        parent::objectIsAnInstanceOf($classname, $constructorArguments);
    }

    public function methodNameIs($method)
    {
        if (null === $this->getBehaviorSubject()) {
            throw new ProphetException('Specify object type first.');
        }

        if (!$this->getBehaviorSubject() instanceof LazyMethod) {
            throw new ProphetException('Object is already initialized.');
        }

        $this->getBehaviorSubject()->setMethodName($method);
    }

    public function methodCalledWith()
    {
        if (null === $this->getBehaviorSubject()) {
            throw new ProphetException('Specify object type first.');
        }

        if (!$this->getBehaviorSubject() instanceof LazyMethod) {
            throw new ProphetException('Object is already initialized.');
        }

        $this->getBehaviorSubject()->setMethodArguments(
            $this->getBehaviorResolver()->resolveAll(func_get_args())
        );
    }

    public function __invoke()
    {
        call_user_func_array(array($this, 'methodCalledWith'), func_get_args());

        return $this;
    }
}
