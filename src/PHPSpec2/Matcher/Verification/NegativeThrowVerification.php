<?php

namespace PHPSpec2\Matcher\Verification;

use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Formatter\Presenter\PresenterInterface;

class NegativeThrowVerification
{
    private $subject;
    private $class;
    private $message;
    private $presenter;

    public function __construct($subject, $arguments, PresenterInterface $presenter)
    {
        $this->subject     = $subject;
        $this->presenter = $presenter;

        if (0 == count($arguments)) {
            return;
        }

        if (is_string($arguments[0])) {
            $this->class   = $arguments[0];
            $this->message = isset($arguments[1]) ? $arguments[1] : null;
        } elseif (is_object($arguments[0]) && $arguments[0] instanceof \Exception) {
            $this->class   = get_class($arguments[0]);
            $this->message = $arguments[0]->getMessage();
        } else {
            throw new MatcherException(sprintf(
                "Wrong argument provided in throw matcher.\n".
                "Fully qualified classname or exception instance expected,\n".
                "Got %s.",
                $this->presenter->presentValue($arguments[0])
            ));
        }
    }

    public function during($callable, array $arguments = array())
    {
        if (!is_callable($callable)) {
            $callable = array($this->subject, $callable);
        }

        try {
            call_user_func_array($callable, $arguments);
        } catch (\Exception $e) {
            if (null === $this->class) {
                throw new FailureException(sprintf(
                    'Expected to not throw any exceptions, but got %s.',
                    $this->presenter->presentValue($e)
                ));
            }

            if ($e instanceof $this->class && null === $this->message) {
                throw new FailureException(sprintf(
                    'Expected to not throw %s exception, but got it.',
                    $this->presenter->presentString($this->class)
                ));
            }

            if ($e instanceof $this->class && $e->getMessage() === $this->message) {
                throw new FailureException(sprintf(
                    "Expected to not throw %s exception\n".
                    "with %s message,\nbut got it.",
                    $this->presenter->presentString($this->class),
                    $this->presenter->presentValue($this->message)
                ));
            }
        }
    }

    public function __call($method, array $arguments = array())
    {
        if (preg_match('/^during(.*)$/', $method, $matches)) {
            $method = lcfirst($matches[1]);

            return $this->during($method, $arguments);
        }

        throw new \RuntimeException($method.' not found');
    }
}
