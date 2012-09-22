<?php

namespace PHPSpec2;

use PHPSpec2\Wrapper\LazyMethod;
use PHPSpec2\Exception\BehaviorException;

class MethodBehavior extends ObjectBehavior
{
    public function methodNameIs($method)
    {
        if (null === $this->getBehaviorSubject()) {
            throw new BehaviorException(
                'You can not set method arguments. Behavior subject is null.'
            );
        }

        if (!$this->getBehaviorSubject() instanceof LazyMethod) {
            throw new BehaviorException(
                'You can not set method name. Behavior subject is already called.'
            );
        }

        $this->getBehaviorSubject()->setMethodName($method);
    }

    public function methodIsCalledWith()
    {
        if (null === $this->getBehaviorSubject()) {
            throw new BehaviorException(
                'You can not set method arguments. Behavior subject is null.'
            );
        }

        if (!$this->getBehaviorSubject() instanceof LazyMethod) {
            throw new BehaviorException(
                'You can not set method arguments. Behavior subject is already called.'
            );
        }

        $this->getBehaviorSubject()->setMethodArguments(
            $this->getBehaviorResolver()->resolveAll(func_get_args())
        );
    }

    public function __invoke()
    {
        call_user_func_array(array($this, 'methodIsCalledWith'), func_get_args());

        return $this;
    }

    protected function createLazySubject()
    {
        return new LazyMethod;
    }
}
