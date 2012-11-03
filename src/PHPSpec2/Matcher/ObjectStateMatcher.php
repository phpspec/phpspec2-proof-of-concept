<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\MethodNotFoundException;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class ObjectStateMatcher implements MatcherInterface
{
    private $regex = '/(be|have)(.+)/';
    private $presenter;

    public function __construct(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }

    public function supports($name, $subject, array $arguments)
    {
        return is_object($subject)
            && preg_match($this->regex, $name)
        ;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        preg_match($this->regex, $name, $matches);
        $method = ('be' === $matches[1] ? 'is' : 'has').$matches[2];

        if (!method_exists($subject, $method)) {
            throw new MethodNotFoundException(sprintf(
                'Method %s not found.',
                $this->presenter->presentString(get_class($subject).'::'.$method.'()')
            ), $subject, $method, $arguments);
        }

        $callable = array($subject, $method);
        if (true !== $result = call_user_func_array($callable, $arguments)) {
            throw $this->getFailureExceptionFor($callable, true, $result);
        }
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        preg_match($this->regex, $name, $matches);
        $method = ('be' === $matches[1] ? 'is' : 'has').$matches[2];

        if (!method_exists($subject, $method)) {
            throw new MethodNotFoundException(sprintf(
                'Method %s not found.',
                $this->presenter->presentString(get_class($subject).'::'.$method.'()')
            ), $subject, $method, $arguments);
        }

        $callable = array($subject, $method);
        if (false !== $result = call_user_func_array($callable, $arguments)) {
            throw $this->getFailureExceptionFor($callable, false, $result);
        }
    }

    public function getPriority()
    {
        return 50;
    }

    private function getFailureExceptionFor($callable, $expectedBool, $result)
    {
        return new FailureException(sprintf(
            "Expected %s to return %s, but got %s.",
            $this->presenter->presentValue($callable),
            $this->presenter->presentValue($expectedBool),
            $this->presenter->presentValue($result)
        ));
    }
}
