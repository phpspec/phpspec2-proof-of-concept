<?php

namespace PHPSpec2\Matcher\Verification;

use PHPSpec2\Exception\Example\StringsNotEqualException;

class PositiveThrowVerification
{
    private $subject;
    private $exceptionClass;

    public function __construct($subject, $exceptionClass)
    {
        $this->subject = $subject;
        $this->exceptionClass = $exceptionClass;
    }

    public function during($callable, array $args)
    {
        try {
            call_user_func_array(array($this->subject, $callable), $args);
        } catch (\Exception $e) {
            if (!$e instanceof $this->exceptionClass) {
                throw new StringsNotEqualException(
                    sprintf('Expected to throw %s, but got %s', $this->exceptionClass, get_class($e)),
                    $this->exceptionClass, get_class($e)
                );
            }
        }
    }
}
