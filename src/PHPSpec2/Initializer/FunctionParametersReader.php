<?php

namespace PHPSpec2\Initializer;

use PHPSpec2\Loader\Node\Example;
use ReflectionFunction;

class FunctionParametersReader
{
    public function getParameters(Example $example)
    {
        $parameters = array();
        $functions  = array_merge(
            $example->getPreFunctions(),
            ($function = $example->getFunction()) ? array($function) : array(),
            $example->getPostFunctions()
        );

        foreach ($functions as $function) {
            foreach ($this->getFunctionParameters($function) as $name => $type) {
                if (!isset($parameters[$name])) {
                    $parameters[$name] = $type;
                }
            }
        }

        return $parameters;
    }

    private function getFunctionParameters(ReflectionFunction $function)
    {
        $parameters = $this->getDocParameters($function);
        foreach ($function->getParameters() as $parameter) {
            if (!isset($parameters[$parameter->getName()])) {
                $parameters[$parameter->getName()] = null;
            }
        }

        return $parameters;
    }

    private function getDocParameters(ReflectionFunction $function)
    {
        $parameters = array();
        if ($comment = $function->getDocComment()) {
            foreach (explode("\n", trim($comment)) as $line) {
                if (preg_match('#@param *([^ ]*) *\$([^ ]*)#', $line, $match)) {
                    $parameters[$match[2]] = $match[1];
                }
            }
        }

        return $parameters;
    }
}
