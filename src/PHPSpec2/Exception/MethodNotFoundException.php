<?php

namespace PHPSpec2\Exception;

class MethodNotFoundException extends Exception
{
    private $subject;
    private $method;

    public function __construct($subject, $method)
    {
        $this->subject = $subject;
        $this->method  = $method;

        parent::__construct(sprintf(
            'Method <value>%s::%s()</value> not found.',
            is_object($subject) ? get_class($subject) : $subject,
            $method
        ));
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getMethod()
    {
        return $this->method;
    }
}
