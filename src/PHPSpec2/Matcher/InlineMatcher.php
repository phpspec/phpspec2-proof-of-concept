<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Exception\Example\FailureException;

class InlineMatcher implements MatcherInterface
{
    private $name;
    private $checker;
    private $message;

    public function __construct($name, $message, $checker)
    {
        if (!is_callable($checker)) {
            throw new \InvalidArgumentException(
                'Checker (last argument to InlineMatcher) should be callable.'
            );
        }

        $this->name    = $name;
        $this->checker = $checker;
        $this->message = $message;
    }

    public function supports($name, $subject, array $arguments)
    {
        return $name === $this->name;
    }

    public function positiveMatch($name, $subject, array $arguments)
    {
        array_unshift($arguments, $subject);
        if (!call_user_func_array($this->checker, $arguments)) {
            throw new FailureException(sprintf(
                'Subject expected to %s, but it is not.', $this->message
            ));
        }
    }

    public function negativeMatch($name, $subject, array $arguments)
    {
        array_unshift($arguments, $subject);
        if (call_user_func_array($this->checker, $arguments)) {
            throw new FailureException(sprintf(
                'Subject expected not to %s, but it is.', $this->message
            ));
        }
    }

    public function getPriority()
    {
        return 150;
    }
}
