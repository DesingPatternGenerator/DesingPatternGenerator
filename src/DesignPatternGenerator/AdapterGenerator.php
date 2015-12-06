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
        $adapterClassName = $this->getSourceClassName($adapter);
        $resultClassName = $sourceClassName . 'Adapter';

        $adapterReflection = new \ReflectionClass($adapter);

        $use = join(PHP_EOL, [
            "use $class;",
            "use $adapter;",
        ]);

        $methods = [
            $this->getResultMethodString([
                ':modifiers:' => 'public',
                ':name:' => '__construct',
                ':parameters:' => $sourceClassName . ' $instance',
            ])
        ];

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

        $behavior = $adapterReflection->isInterface()
            ? 'implements'
            : 'extends';

        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':use:' => $use,
            ':header:' => "class $resultClassName $behavior $adapterClassName",
            ':body:' => join(PHP_EOL, $methods),
        ]);

        $this->store($path, $resultClassName, $result);

        return true;
    }
}