<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Prophet\MethodNotFoundException;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Representer\RepresenterInterface;
use PHPSpec2\Formatter\Representer\BasicRepresenter;

class ObjectStateMatcher implements MatcherInterface
{
    private $regex = '/(be|have)(.+)/';
    private $representer;

    public function __construct(RepresenterInterface $representer = null)
    {
        $this->representer = $representer ?: new BasicRepresenter;;
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
            throw new MethodNotFoundException($subject, $method);
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
            throw new MethodNotFoundException($subject, $method);
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
            "Expected <value>%s</value> to return <value>%s</value>, but got <value>%s</value>.",
            $this->representer->representValue($callable),
            $this->representer->representValue($expectedBool),
            $this->representer->representValue($result)
        ));
    }
}
