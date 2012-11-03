<?php

namespace PHPSpec2\Exception;

class PropertyNotFoundException extends Exception
{
    private $subject;
    private $property;

    public function __construct($message, $subject, $property)
    {
        parent::__construct($message);

        $this->subject = $subject;
        $this->property  = $property;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
