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
        $methodSpecifications = array();
        foreach ($newClassnames as $classname) {
            $class = new ReflectionClass($classname);

            if ($class->isAbstract()) {
                continue;
            }

            if (!$class->implementsInterface('PHPSpec2\\SpecificationInterface')) {
                continue;
            }

            $preFunctions = array();
            if ($class->hasMethod('described_with')) {
                $preFunctions[] = $class->getMethod('described_with');
            }

            $subject = $this->getClassSubject($class->getName());
            $specification = new Node\Specification($subject, $subject);

            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
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

            if (count($specification->getChildren())) {
                if (false === strpos($specification->getSubject(), '::')) {
                    $specifications[] = $specification;
                } else {
                    $methodSpecifications[] = $specification;
                }
            }
        }

        foreach ($methodSpecifications as $methodSpecification) {
            list($class, $method) = explode('::', $methodSpecification->getSubject());
            $parentSpecs = array_filter($specifications, function($specification) use($class) {
                return $class === $specification->getSubject();
            });

            if (count($parentSpecs)) {
                $spec = current($parentSpecs);
                $spec->addChild($methodSpecification);
                $methodSpecification->setTitle($method.'()');
            } else {
                $methodSpecification->setTitle($methodSpecification->getTitle().'()');
                $specifications[] = $methodSpecification;
            }
        }

        return $specifications;
    }

    private function getClassSubject($classname)
    {
        $subject = preg_replace("|^spec\\\|", '', $classname);

        if (2 === count($parts = explode('_', $subject))) {
            $subject = $parts[0].'::'.$parts[1];
        }

        return $subject;
    }

    private function lineIsInsideMethod($line, ReflectionMethod $method)
    {
        return $line >= $method->getStartLine() && $line <= $method->getEndLine();
    }
}
