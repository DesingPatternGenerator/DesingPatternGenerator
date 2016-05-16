<?php

namespace ReenExe\DesignPatternGenerator;

class NullObjectGenerator extends Generator
{
    public function generate(array $settings): bool
    {
        $class = $settings['class'];

        $reflection = new \ReflectionClass($class);

        if ($reflection->isFinal()) {
            return false;
        }

        $this
            ->clearUse()
            ->addUseClass($class);

        $sourceClassName = $reflection->getShortName();
        $resultClassName = 'Null' . $sourceClassName;
        $methods = $this->getClassMethods($reflection);
        $namespace = $settings['namespace'];
        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':header:' => "class $resultClassName {$this->getBehavior($reflection)} $sourceClassName",
            ':body:' => join(PHP_EOL, $methods),
        ]);

        $path = $settings['path'];
        $this->store($path, $resultClassName, $result);

        return true;
    }
}
