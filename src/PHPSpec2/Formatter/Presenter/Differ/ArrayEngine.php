<?php

namespace PHPSpec2\Formatter\Presenter\Differ;

class ArrayEngine implements EngineInterface
{
    CONST DEFAULT_SEPARATOR = '->';
    CONST VALUE_NOT_EXPECTED_TO_BE = 1;
    CONST VALUE_EXPECTED_TO_BE = 2;
    CONST VALUE_EXPECTED_TO_BE_DIFFERENT = 3;

    public function supports($expected, $actual)
    {
        return is_array($expected) && is_array($actual);
    }

    public function compare($expected, $actual)
    {
        $differences = $this->doComparison($expected, $actual, self::VALUE_NOT_EXPECTED_TO_BE);
        $differences = array_merge($this->doComparison($actual, $expected, self::VALUE_EXPECTED_TO_BE), $differences);

        $output = '';
        foreach ($differences as $key => $value) {
            $output .= sprintf("%s %s\n", trim($key), $value); 
        }

        return sprintf("<code>\n%s</code>", $output);
    }

    /**
     * function that responses with an array key=>'output string'
     *
     * @param array $key_path array of the keys of the current values
     * @param mixed $expected
     * @param mixed $actual
     * @param int $value_expectation could be (VALUE_NOT_EXPECTED_TO_BE, VALUE_EXPECTED_TO_BE_DIFFERENT)
     * @return array
     */
    function makeResponse($key_path, $expected, $actual, $expectation = self::VALUE_NOT_EXPECTED_TO_BE, $separator = self::DEFAULT_SEPARATOR)
    {
       
        $currentKey = array_pop($key_path);
        if ($expectation == self::VALUE_NOT_EXPECTED_TO_BE) {
            $expectation = sprintf("%s exists, but expected not to be", $expected);
        } elseif ($expectation == self::VALUE_EXPECTED_TO_BE) { 
            $expectation = sprintf("%s does not exists, but expected to be", $expected);
        } elseif ($expectation == self::VALUE_EXPECTED_TO_BE_DIFFERENT) {
            $expectation =  sprintf("is \"%s\", but expected to be \"%s\"", $expected, $actual);
        }
        $key = sprintf("%s %s", implode($separator, $key_path), $currentKey);
        return array($key => $expectation);
    }

    /**
     * function that handles the differences between two array
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param int $value_expectation could be (VALUE_NOT_EXPECTED_TO_BE, VALUE_EXPECTED_TO_BE_DIFFERENT)
     * @param array $key_path array of the keys of the current values
     * @return array an associative array of difference
     */
    function doComparison($expected, $actual, $value_expectation = 1, $key_path = array())
    {
        
        if ($expected == $actual) {
            return array(); 
        } elseif (is_array($expected) && is_array($actual)) {
            $brothers = array();
            foreach ($expected as $key => $value) {
                array_push($key_path, $key);
                if (isset($actual[$key])) {
                    $comparison = $this->doComparison($value, $actual[$key], $value_expectation, $key_path);
                    $brothers = array_merge($brothers, $comparison);
                } else {
                    // if not exists on b
                    $brothers = array_merge($brothers, $this->makeResponse($key_path, $expected, $actual, $value_expectation));
                }
                array_pop($key_path);
            }
            return $brothers;
        } else {
            return $this->makeResponse($key_path, $expected, $actual, self::VALUE_EXPECTED_TO_BE_DIFFERENT);
        }
    }
}
