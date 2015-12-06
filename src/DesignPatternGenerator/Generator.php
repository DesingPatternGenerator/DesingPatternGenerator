<?php

namespace ReenExe\DesignPatternGenerator;

use Symfony\Component\Filesystem\Filesystem;

abstract class Generator
{
    protected $classTemplate = <<<'PHP'
<?php

:namespace:

:use:

:header:
{
:body:
}

PHP;

    protected $methodTemplate = <<<'PHP'

    :comment:
    :modifiers: function :name:(:parameters:):return:
    {
        :body:
    }
PHP;

    /**
     * @param array $settings
     * @return bool
     */
    abstract public function generate(array $settings): bool;

    protected function getResultClassString(array $data): string
    {
        return strtr($this->classTemplate, $data);
    }

    protected function getResultMethodString(array $data): string
    {
        static $default = [
            ':comment:' => '',
            ':body:' => '',
            ':return:' => '',
        ];

        return strtr($this->methodTemplate, array_merge($default, $data));
    }

    protected function getSourceClassName(string $class)
    {
        $sourceClassNamespacePath = explode('\\', $class);
        return end($sourceClassNamespacePath);
    }

    protected function getClassMethods(\ReflectionClass $class)
    {
        $excludeModifiers = \ReflectionMethod::IS_FINAL | \ReflectionMethod::IS_PRIVATE;

        $methods = [];
        foreach ($class->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->isConstructor()) continue;

            $sourceModifiers = $reflectionMethod->getModifiers();

            if ($sourceModifiers & $excludeModifiers) continue;

            if ($sourceModifiers & \ReflectionMethod::IS_ABSTRACT) {
                $sourceModifiers ^= \ReflectionMethod::IS_ABSTRACT;
            }

            $modifiers = join(
                ' ', \Reflection::getModifierNames($sourceModifiers)
            );

            $parameters = array_map(
                [$this, 'getMethodParameter'],
                $reflectionMethod->getParameters()
            );

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
        return $methods;
    }

    protected function getMethodParameter(\ReflectionParameter $reflectionParameter)
    {
        $settings = [];

        if ($class = $reflectionParameter->getClass()) {
            $settings[] = $class->isInternal()
                ? '\\' . $class->getName()
                : $class->getShortName();

        } elseif ($reflectionParameter->getType()) {
            $settings[] = $reflectionParameter->getType();
        }

        if ($reflectionParameter->isVariadic()) {
            $settings[] = '...';
        }

        $settings[] = $name = '$' . $reflectionParameter->getName();

        $parameter = implode(' ', $settings);

        if ($reflectionParameter->isDefaultValueAvailable()) {
            $parameter .= " = {$this->getParameterDefaultValue($reflectionParameter)}";
        }

        return $parameter;
    }

    protected function getParameterDefaultValue(\ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueConstant()) {
            return '\\' . $parameter->getDefaultValueConstantName();
        }

        $value = $parameter->getDefaultValue();

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value)) {
            return "'$value'";
        }

        if (is_array($value)) {
            return '[]';
        }
    }

    protected function getBehavior(\ReflectionClass $class)
    {
        return $class->isInterface() ? 'implements' : 'extends';
    }

    protected function store($path, $resultClassName, $content)
    {
        $fs = new Filesystem();

        $fs->dumpFile("$path/$resultClassName.php", $content);
    }
}
