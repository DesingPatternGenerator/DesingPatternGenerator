<?php

namespace ReenExe\DesignPatternGenerator;

use Symfony\Component\Filesystem\Filesystem;

class DecoratorGenerator extends Generator
{
    public function generate(string $class, string $namespace, string $path): bool
    {
        $fs = new Filesystem();

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

        foreach ($reflection->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->isConstructor()) continue;

            $sourceModifiers = $reflectionMethod->getModifiers();

            if ($sourceModifiers & \ReflectionMethod::IS_FINAL) continue;

            if ($sourceModifiers & \ReflectionMethod::IS_ABSTRACT) {
                $sourceModifiers ^= \ReflectionMethod::IS_ABSTRACT;
            }

            $modifiers = join(
                ' ', \Reflection::getModifierNames($sourceModifiers)
            );

            $parameters = [];
            foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                $name = '$' . $reflectionParameter->getName();
                $parameters[] = $reflectionParameter->getType()
                    ? "{$reflectionParameter->getType()} $name"
                    : $name;
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

        $fs->dumpFile("$path/$resultClassName.php", $result);

        return true;
    }
}
