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
        $checkerName = $this->getCheckerName($name);

        return is_scalar($subject)
               && $checkerName !== false
               && function_exists($checkerName);
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
        $checkerName = $this->getCheckerName($name);

        if (false === call_user_func_array($checkerName, array($subject))) {
            throw new FailureException(sprintf(
                'Expected %s, but got %s.',
                substr($checkerName, 3),
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
        $checkerName = $this->getCheckerName($name);

        if (true === call_user_func_array($checkerName, array($subject))) {
            throw new FailureException(sprintf(
                'Expected %s, but got %s.',
                substr($checkerName, 3),
                gettype($subject)
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

    /**
     * @param string $name
     *
     * @return string|boolean
     */
    private function getCheckerName($name)
    {
        $isSupported = preg_match($this->regex, $name, $matches);
        if ($isSupported) {
            $expected = strtolower($matches[2]);

            if ($expected == 'boolean') {
                return 'is_bool';
            }
            return 'is_' . $expected;
        }
        return false;
    }

}