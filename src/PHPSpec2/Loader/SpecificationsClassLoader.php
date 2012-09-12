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

            if (!$class->implementsInterface('PHPSpec2\\Specification')) {
                continue;
            }

            $preFunctions = array();
            if ($class->hasMethod('described_with')) {
                $preFunctions[] = $class->getMethod('described_with');
            }

            $specification = new Node\Specification($class->getName());
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (!preg_match('/^(it_|its_)/', $method->getName())) {
                    continue;
                }

                if (null !== $line && !$this->lineIsInsideMethod($line, $method)) {
                    continue;
                }

                $example = new Node\Example(str_replace('_', ' ', $method->getName()), $method);
                array_map(array($example, 'addPreFunction'), $preFunctions);

                $specification->addChild($example);
            }

            $specifications[] = $specification;
        }

        return $specifications;
    }

    private function lineIsInsideMethod($line, ReflectionMethod $method)
    {
        return $line >= $method->getStartLine() && $line <= $method->getEndLine();
    }
}
