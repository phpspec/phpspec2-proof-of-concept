<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Exception\Example\NotEqualException;


class ScalarMatcher extends BasicMatcher
{

    private $presenter;

    public function __construct(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
    }

    protected function matches($subject, array $arguments)
    {

        switch ($arguments[0]) {
            case 'int':
                return 'integer' == gettype($subject);
            case 'float':
                return 'double' == gettype($subject);
            case 'null':
                return 'NULL' == gettype($subject);
            case 'callable':
                return is_callable($subject);
            default:
                return $arguments[0] == gettype($subject);
        }
    }

    protected function getFailureException($name, $subject, array $arguments)
    {
        return new NotEqualException(sprintf(
            'Expected %s, but got %s.',
            $this->presenter->presentValue($arguments[0]),
            $this->presenter->presentValue(gettype($subject))
        ), gettype($arguments[0]), gettype($subject));
    }

    protected function getNegativeFailureException($name, $subject, array $arguments)
    {
        return new FailureException(sprintf(
            'Not expected %s, but got one.',
            $this->presenter->presentValue($arguments[0])
        ));
    }

    /**
     * Checks if matcher supports provided subject and matcher name.
     *
     * @param string $name
     * @param mixed  $subject
     * @param array  $arguments
     *
     * @return Boolean
     */
    public function supports($name, $subject, array $arguments)
    {

        return in_array($name, array('beScalar'))
            && 1 == count($arguments)
            && in_array(
                $arguments[0],
                array('float', 'string', 'int', 'boolean', 'array', 'resource', 'null', 'callable')
            );
    }

}
