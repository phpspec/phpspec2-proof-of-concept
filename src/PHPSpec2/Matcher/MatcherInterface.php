<?php

namespace PHPSpec2\Matcher;

interface MatcherInterface
{
    /**
     * Checks if matcher supports provided subject, name and arguments.
     *
     * @param mixed  $subject
     * @param string $name
     * @param array  $arguments
     *
     * @return Boolean
     */
    public function supports($subject, $name, array $arguments);

    /**
     * Evaluates positive match.
     *
     * @param mixed $subject
     * @param array $arguments
     */
    public function positive($subject, array $arguments);

    /**
     * Evaluates negative match.
     *
     * @param mixed $subject
     * @param array $arguments
     */
    public function negative($subject, array $arguments);
}
