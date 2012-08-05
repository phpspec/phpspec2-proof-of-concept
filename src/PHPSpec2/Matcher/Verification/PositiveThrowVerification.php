<?php

namespace PHPSpec2\Matcher\Verification;

use PHPSpec2\Exception\Example\StringsNotEqualException;
use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\ObjectsNotEqualException;

class PositiveThrowVerification
{
    private $subject;
    private $arguments;
    private $exception;
    private $exceptionClass;

    public function __construct($subject, $arguments)
    {
        $this->subject   = $subject;
        $this->arguments = $arguments;

        if (is_string($this->arguments[0])) {
            $this->exceptionClass = $this->arguments[0];
        } elseif (is_object($this->arguments[0]) && $this->arguments[0] instanceof \Exception) {
            $this->exception = $this->arguments[0];
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
            if ($this->exceptionClass) {
                $this->checkIfExceptionClassCorrect($e);
            } else {
                $this->checkIfExceptionSame($e);
            }
        }
    }

    private function checkIfExceptionClassCorrect(\Exception $e)
    {
        if (!$e instanceof $this->exceptionClass) {
            throw new StringsNotEqualException(
                sprintf('Expected to throw %s, but got %s', $this->exceptionClass, get_class($e)),
                $this->exceptionClass, get_class($e)
            );
        }
    }

    private function checkIfExceptionSame(\Exception $e)
    {
        if ($e == $this->exception) {
            throw new ObjectsNotEqualException(
                sprintf('Expected to throw [%s], but got [%s]', get_class($this->exception), get_class($e)),
                $this->exception, $e
            );
        }
    }
}
