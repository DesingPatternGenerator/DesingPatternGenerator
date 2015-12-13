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

    protected $propertyTemplate = <<<'PHP'
    :comment:
    :modifiers: $:name::define:;
PHP;

    protected $uses = [];
    /**
     * @param array $settings
     * @return bool
     */
    abstract public function generate(array $settings): bool;

    protected function getResultClassString(array $data): string
    {
        $data[':use:'] = $this->getUseString();

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

    protected function getResultPropertyString(array $data): string
    {
        static $default = [
            ':comment:' => '',
            ':define:' => '',
        ];

        return strtr($this->propertyTemplate, $result = array_merge($default, $data));
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

            $resultType = '';
            if ($returnReflectionType = $reflectionMethod->getReturnType()) {

                if ($returnReflectionType->isBuiltin()) {
                    $resultType = ":{$returnReflectionType}";
                } else {
                    $returnClassName = (string)$returnReflectionType;
                    $returnTypeReflectionClass = new \ReflectionClass($returnClassName);

                    $this->addUseClass($returnClassName);

                    $resultType = ":{$returnTypeReflectionClass->getShortName()}";
                }
            }

            $methods[] = $this->getResultMethodString([
                ':comment:' => $reflectionMethod->getDocComment(),
                ':modifiers:' => $modifiers,
                ':name:' => $reflectionMethod->getName(),
                ':parameters:' => join(', ', $parameters),
                ':return:' => $resultType,
                ':body:' => $this->getMethodBody($reflectionMethod)
            ]);
        }

        return $methods;
    }

    protected function getMethodBody(\ReflectionMethod $reflectionMethod)
    {
        return '/* TODO */';
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

        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
    }

    protected function clearUse()
    {
        $this->uses = [];

        return $this;
    }

    protected function addUseClass(string $class)
    {
        $this->uses[$class] = "use $class;";

        return $this;
    }

    protected function getUseString()
    {
        return join(PHP_EOL, $this->uses);
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
