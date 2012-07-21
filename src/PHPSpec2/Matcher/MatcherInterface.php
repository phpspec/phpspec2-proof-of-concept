<?php

namespace PHPSpec2\Matcher;

interface MatcherInterface
{
    /**
     * Checks if matcher supports provided subject, name and parameters.
     *
     * @param mixed  $subject
     * @param string $name
     * @param array  $parameters
     *
     * @return Boolean
     */
    public function supports($subject, $name, array $parameters);

    /**
     * Evaluates positive match.
     *
     * @param mixed $subject
     * @param array $parameters
     */
    public function positiveMatch($subject, array $parameters);

    /**
     * Evaluates negative match.
     *
     * @param mixed $subject
     * @param array $parameters
     */
    public function negativeMatch($subject, array $parameters);
}
