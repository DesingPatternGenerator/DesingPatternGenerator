<?php

namespace ReenExe\DesignPatternGenerator;

class DecoratorGenerator extends Generator
{
    /**
     * @param string $class
     * @param string $namespace
     * @param string $path
     * @return bool
     */
    public function generate(string $class, string $namespace, string $path): bool
    {
        $sourceClassName = $this->getSourceClassName($class);
        $resultClassName = $sourceClassName . 'Decorator';

        $reflection = new \ReflectionClass($class);

        $methods = [
            $this->getResultMethodString([
                ':modifiers:' => 'public',
                ':name:' => '__construct',
                ':parameters:' => $sourceClassName . ' $instance',
            ])
        ];

        $excludeModifiers = \ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PRIVATE;
        foreach ($reflection->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->isConstructor()) continue;

            $sourceModifiers = $reflectionMethod->getModifiers();

            if ($sourceModifiers & $excludeModifiers) continue;

            if ($sourceModifiers & \ReflectionMethod::IS_ABSTRACT) {
                $sourceModifiers ^= \ReflectionMethod::IS_ABSTRACT;
            }

            $modifiers = join(
                ' ', \Reflection::getModifierNames($sourceModifiers)
            );

            $parameters = [];
            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                $settings = [];

                if ($reflectionParameter->getType()) {
                    $settings[] = $reflectionParameter->getType();
                }

                if ($reflectionParameter->isVariadic()) {
                    $settings[] = '...';
                }

                $settings[] = $name = '$' . $reflectionParameter->getName();

                $parameter = implode(' ', $settings);

                if ($reflectionParameter->isDefaultValueAvailable()) {
                    $parameter .= " = '{$reflectionParameter->getDefaultValue()}'";
                }

                $parameters[] = $parameter;
            }

            $resultType = $reflectionMethod->getReturnType()
                ? ":{$reflectionMethod->getReturnType()}"
                : '';

            $methods[] = $this->getResultMethodString([
                ':comment:' => $reflectionMethod->getDocComment(),
                ':modifiers:' => $modifiers,
                ':name:' => $reflectionMethod->getName(),
                ':parameters:' => join(', ', $parameters),
                ':return:' => $resultType,
            ]);
        }

        $behavior = $reflection->isInterface()
            ? 'implements'
            : 'extends';

        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':use:' => "use $class;",
            ':header:' => "class $resultClassName $behavior $sourceClassName",
            ':body:' => join(PHP_EOL, $methods),
        ]);

        $this->store($path, $resultClassName, $result);

        return true;
    }
}
