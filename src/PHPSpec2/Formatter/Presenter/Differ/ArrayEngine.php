<?php

namespace PHPSpec2\Formatter\Presenter\Differ;

class ArrayEngine implements EngineInterface
{
    CONST ARRAY_VARIABLE_NAME = '$expected';
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
     * Add the square bracket to the key, if is a string add the double quotation
     *
     * @param int|String $key
     * @return string  
     */
    private function roundKeyWithSeparator($key) 
    {
        if (!is_numeric($key)) {
                $key = sprintf('"%s"', $key);
        }
        return sprintf("[%s]", $key);
    }

    /**
     * Convert an array into a string that will look like an array
     * 
     *
     * @param array $key_path
     * @param mixed $current_key
     * @return string   
     */
    private function explodeKeyPath($key_path, $current_key, $variable_name = self::ARRAY_VARIABLE_NAME) 
    {
        $exploded = '';
        foreach ($key_path as $key) {
            $exploded .= $this->roundKeyWithSeparator($key);
        }
        $currentKey = $this->roundKeyWithSeparator($current_key);
        if (empty($exploded)) {
            $exploded = sprintf("%s%s", $variable_name, $currentKey);
        } else {
            $exploded = sprintf("%s%s%s", $variable_name, $exploded, $currentKey);
        }
        return $exploded;
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
    private function makeResponse($key_path, $expected, $actual, $expectation = self::VALUE_NOT_EXPECTED_TO_BE)
    {
       
        $currentKey = array_pop($key_path);
        if ($expectation == self::VALUE_NOT_EXPECTED_TO_BE) {
            $expectation = sprintf("%s exists, but expected not to be", $expected);
        } elseif ($expectation == self::VALUE_EXPECTED_TO_BE) { 
            $expectation = sprintf("%s does not exists, but expected to be", $expected);
        } elseif ($expectation == self::VALUE_EXPECTED_TO_BE_DIFFERENT) {
            $expectation =  sprintf("is \"%s\", but expected to be \"%s\"", $expected, $actual);
        }
        
        $key = $this->explodeKeyPath($key_path, $currentKey);
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
    private function doComparison($expected, $actual, $value_expectation = 1, $key_path = array())
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
