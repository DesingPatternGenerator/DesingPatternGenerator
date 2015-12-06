<?php

namespace ReenExe\DesignPatternGenerator;

class DecoratorGenerator extends Generator
{
    /**
     * @param array $settings
     * @return bool
     */
    public function generate(array $settings): bool
    {
        $class = $settings['class'];
        $namespace = $settings['namespace'];
        $path = $settings['path'];

        $sourceClassName = $this->getSourceClassName($class);
        $resultClassName = $sourceClassName . 'Decorator';

        $reflection = new \ReflectionClass($class);

        $methods = array_merge(
            [
                $this->getResultMethodString([
                    ':modifiers:' => 'public',
                    ':name:' => '__construct',
                    ':parameters:' => $sourceClassName . ' $instance',
                ])
            ],
            $this->getClassMethods($reflection)
        );

        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':use:' => "use $class;",
            ':header:' => "class $resultClassName {$this->getBehavior($reflection)} $sourceClassName",
            ':body:' => join(PHP_EOL, $methods),
        ]);

        $this->store($path, $resultClassName, $result);

        return true;
    }
}
