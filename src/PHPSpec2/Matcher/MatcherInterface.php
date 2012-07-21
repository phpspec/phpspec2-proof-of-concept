<?php

namespace PHPSpec2\Matcher;

interface MatcherInterface
{
    /**
     * Checks if matcher supports provided subject and matcher name.
     *
     * @param mixed  $subject
     * @param string $name
     *
     * @return Boolean
     */
    public function supports($subject, $name);

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
