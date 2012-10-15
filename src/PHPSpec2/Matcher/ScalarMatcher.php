<?php

namespace PHPSpec2\Matcher;

use PHPSpec2\Formatter\Presenter\PresenterInterface;
use PHPSpec2\Exception\Example\FailureException;
use PHPSpec2\Exception\FunctionNotFoundException;


class ScalarMatcher implements MatcherInterface
{

    /**
     * @var \PHPSpec2\Formatter\Presenter\PresenterInterface $presenter
     */
    private $presenter;

    /**
     * @var string $regex
     */
    private $regex = '/(be)(.+)/';

    /**
     * @param \PHPSpec2\Formatter\Presenter\PresenterInterface $presenter
     */
    public function __construct(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;
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
        return is_scalar($subject)
               && preg_match($this->regex, $name);
    }

    /**
     * Evaluates positive match.
     *
     * @param string $name
     * @param mixed  $subject
     * @param array  $arguments
     *
     * @throws \PHPSpec2\Exception\FunctionNotFoundException
     * @throws \PHPSpec2\Exception\Example\FailureException
     * @return boolean
     */
    public function positiveMatch($name, $subject, array $arguments)
    {
        preg_match($this->regex, $name, $matches);

        $expected = strtolower($matches[2]);

        switch ($expected) {

            case 'boolean':
                $func = 'is_bool';
                break;

            default:
                $func = 'is_' . $expected;
                break;
        }

        if (!function_exists($func)) {
            throw new FunctionNotFoundException($func);
        }

        if (false === call_user_func_array($func, array($subject))) {
            throw new FailureException(sprintf(
                'Expected %s, but got %s.',
                $expected,
                gettype($subject)
            ));
        }
    }

    /**
     * Evaluates negative match.
     *
     * @param string $name
     * @param mixed  $subject
     * @param array  $arguments
     *
     * @throws \PHPSpec2\Exception\FunctionNotFoundException
     * @throws \PHPSpec2\Exception\Example\FailureException
     * @return boolean
     */
    public function negativeMatch($name, $subject, array $arguments)
    {
        preg_match($this->regex, $name, $matches);
        $expected = strtolower($matches[2]);

        switch ($expected) {
            case 'boolean':
                $func = 'is_bool';
                break;

            default:
                $func = 'is_' . $expected;
                break;
        }

        if (!function_exists($func)) {
            throw new FunctionNotFoundException($func);
        }

        if (true === call_user_func_array($func, array($subject))) {
            throw new FailureException(sprintf(
                'Expected %s.',
                $expected
            ));
        }
    }

    /**
     * Returns matcher priority.
     *
     * @return integer
     */
    public function getPriority()
    {
        return 50;
    }

}