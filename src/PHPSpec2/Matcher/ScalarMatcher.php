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

    protected function matches($type, array $arguments)
    {
        switch ($type) {
            case 'int':
                return 'integer' == gettype($arguments[0]);
            case 'float':
                return 'double' == gettype($arguments[0]);
            case 'null':
                return 'NULL' == gettype($arguments[0]);
            case 'callable':
                return is_callable($arguments[0]);
            default:
                return $type == gettype($arguments[0]);
        }
    }

    protected function getFailureException($name, $type, array $arguments)
    {
        return new NotEqualException(sprintf(
            'Expected %s, but got %s.',
            $this->presenter->presentValue($arguments[0]),
            $this->presenter->presentValue($type)
        ), $type, gettype($arguments[0]));
    }

    protected function getNegativeFailureException($name, $type, array $arguments)
    {
        return new FailureException(sprintf(
            'Not expected %s, but got one.',
            $this->presenter->presentValue($type)
        ));
    }

    /**
     * Checks if matcher supports provided subject and matcher name.
     *
     * @param string $name
     * @param mixed  $type
     * @param array  $arguments
     *
     * @return Boolean
     */
    public function supports($name, $type, array $arguments)
    {
        return in_array($name, array('beScalar'))
            && in_array($type, array('float', 'string', 'int', 'boolean', 'array', 'object', 'resource', 'null', 'callable'))
            && 1 == count($arguments);
    }

}
