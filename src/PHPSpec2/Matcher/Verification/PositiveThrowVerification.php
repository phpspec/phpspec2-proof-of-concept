<?php

namespace PHPSpec2\Matcher\Verification;

use PHPSpec2\Exception\Example\StringsNotEqualException;
use PHPSpec2\Exception\Example\MatcherException;
use PHPSpec2\Exception\Example\ObjectsNotEqualException;
use PHPSpec2\Exception\Example\FailureException;

class PositiveThrowVerification
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
                $this->checkIfExceptionClassCorrect($e);
            } else {
                $this->checkIfExceptionSame($e);
            }

            return;
        }

        throw new FailureException('Expected to get exception, none got.');
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
        if (get_class($e) !== get_class($this->exception)
         || $e->getMessage() !== $this->exception->getMessage()) {
            throw new ObjectsNotEqualException(
                sprintf('Expected to throw [%s], but got [%s]', get_class($this->exception), get_class($e)),
                $this->exception, $e
            );
        }
    }
}
