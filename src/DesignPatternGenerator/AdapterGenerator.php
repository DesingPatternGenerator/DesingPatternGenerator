<?php

namespace ReenExe\DesignPatternGenerator;

class AdapterGenerator extends Generator
{
    /**
     * @param array $settings
     * @return bool
     */
    public function generate(array $settings): bool
    {
        $class = $settings['class'];
        $adapter = $settings['adapter'];
        $namespace = $settings['namespace'];
        $path = $settings['path'];

        $sourceClassName = $this->getSourceClassName($class);
        $resultClassName = $sourceClassName . 'Adapter';

        $adapterReflection = new \ReflectionClass($adapter);
        $adapterClassName = $adapterReflection->getShortName();

        $this
            ->clearUse()
            ->addUseClass($class)
            ->addUseClass($adapter);

        $methods = array_merge(
            [
                $this->getResultMethodString([
                    ':modifiers:' => 'public',
                    ':name:' => '__construct',
                    ':parameters:' => $sourceClassName . ' $instance',
                ])
            ],
            $this->getClassMethods($adapterReflection)
        );

        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':header:' => "class $resultClassName {$this->getBehavior($adapterReflection)} $adapterClassName",
            ':body:' => join(PHP_EOL, $methods),
        ]);

        $this->store($path, $resultClassName, $result);

        return true;
    }
}