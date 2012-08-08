<?php

namespace PHPSpec2\Matcher\Verification;

use PHPSpec2\Exception\Example\StringsNotEqualException;
use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\ObjectsNotEqualException;
use PHPSpec2\Exception\Example\FailureException;

class NegativeThrowVerification
{
    private $subject;
    private $exception;
    private $exceptionClass;

    public function __construct($subject, $arguments)
    {
        $this->subject = $subject;

        if (is_string($arguments[0])) {
            $this->exceptionClass = $arguments[0];
        } elseif (is_object($arguments[0]) && $arguments[0] instanceof \Exception) {
            $this->exception = $arguments[0];
        } else {
            throw new MatcherException(
                'Wrong argument provided in throw matcher. Provide fully qualified classname or exception instance.'
            );
        }
    }

    public function during($callable, array $args)
    {
        try {
            call_user_func_array(array($this->subject, $callable), $args);
        } catch (\Exception $e) {
            if (null !== $this->exceptionClass) {
                $this->checkIfExceptionAreNotSame($e);
            }
        }
    }

    private function checkIfExceptionAreNotSame(\Exception $e)
    {
        if ($e instanceof $this->exceptionClass) {
            throw new FailureException(
                sprintf('Expected not to throw %s, but it did', $this->exceptionClass)
            );
        }
    }

}
