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

        $methods = [];
        foreach ($reflection->getMethods() as $reflectionMethod) {
            $modifiers = join(
                ' ',
                \Reflection::getModifierNames($reflectionMethod->getModifiers())
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
                ':body:' => '',
                ':parameters:' => join(', ', $parameters),
                ':return:' => $resultType,
            ]);
        }

        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':use:' => "use $class;",
            ':header:' => "class $resultClassName extends $sourceClassName",
            ':body:' => join(PHP_EOL, $methods),
        ]);

        $fs->dumpFile("$path/$resultClassName.php", $result);

        return true;
    }
}
