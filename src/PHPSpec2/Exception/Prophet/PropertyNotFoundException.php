<?php

namespace PHPSpec2\Exception\Prophet;

class PropertyNotFoundException extends ProphetException
{
    private $subject;
    private $property;

    public function __construct($subject, $property)
    {
        $this->subject = $subject;
        $this->property  = $property;

        parent::__construct(sprintf(
            'Property <value>%s::%s</value> not found.',
            get_class($subject), $property
        ));
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
