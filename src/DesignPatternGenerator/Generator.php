<?php

namespace ReenExe\DesignPatternGenerator;

abstract class Generator
{
    protected $template = <<<'PHP'
<?php

:namespace:

:use:

:header:
{
:body:
}

PHP;

    abstract public function generate(string $class, string $namespace, string $path): bool;

    protected function getResultString(array $data): string
    {
        $map = array_merge($this->getDefault(), $data);

        return strtr($this->template, $map);
    }

    protected function getDefault(): array
    {
        return [
            ':namespace:' => '',
            ':use:' => '',
        ];
    }

    protected function getSourceClassName(string $class)
    {
        $sourceClassNamespacePath = explode('\\', $class);
        return end($sourceClassNamespacePath);
    }
}
