<?php

namespace ReenExe\DesignPatternGenerator;

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


    abstract public function generate(string $class, string $namespace, string $path): bool;

    protected function getResultClassString(array $data): string
    {
        return strtr($this->classTemplate, $data);
    }

    protected function getResultMethodString(array $data): string
    {
        return strtr($this->methodTemplate, $data);
    }

    protected function getSourceClassName(string $class)
    {
        $sourceClassNamespacePath = explode('\\', $class);
        return end($sourceClassNamespacePath);
    }
}
