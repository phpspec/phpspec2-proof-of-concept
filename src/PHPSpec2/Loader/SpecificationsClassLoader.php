<?php

namespace PHPSpec2\Loader;

use ReflectionClass;
use ReflectionMethod;

class SpecificationsClassLoader implements LoaderInterface
{
    public function loadFromFile($filename, $line = null)
    {
        $oldClassnames = get_declared_classes();
        require_once $filename;
        $newClassnames = array_diff(get_declared_classes(), $oldClassnames);

        $specifications = array();
        foreach ($newClassnames as $classname) {
            $class = new ReflectionClass($classname);

            if ($class->isAbstract()) {
                continue;
            }

            if (!$class->implementsInterface('PHPSpec2\\SpecificationInterface')) {
                continue;
            }

            $preFunctions = array();
            if ($class->hasMethod('let')) {
                $preFunctions[] = $class->getMethod('let');
            }
            $postFunctions = array();
            if ($class->hasMethod('letgo')) {
                $postFunctions[] = $class->getMethod('letgo');
            }

            $specification = new Node\Specification($class->getName());
            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (!preg_match('/^(it|its)[^a-zA-Z]/', $method->getName())) {
                    continue;
                }

                if (null !== $line && !$this->lineIsInsideMethod($line, $method)) {
                    continue;
                }

                $example = new Node\Example(str_replace('_', ' ', $method->getName()), $method);
                array_map(array($example, 'addPreFunction'), $preFunctions);
                array_map(array($example, 'addPostFunction'), $postFunctions);

                $specification->addChild($example);
            }

            if (count($specification->getChildren())) {
                $specifications[] = $specification;
            }
        }

        return $specifications;
    }

    private function lineIsInsideMethod($line, ReflectionMethod $method)
    {
        return $line >= $method->getStartLine() && $line <= $method->getEndLine();
    }
}
