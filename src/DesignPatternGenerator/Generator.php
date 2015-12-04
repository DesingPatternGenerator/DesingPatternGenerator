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
     * @param string $class
     * @param string $namespace
     * @param string $path
     * @return bool
     */
    abstract public function generate(string $class, string $namespace, string $path): bool;

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

    protected function store($path, $resultClassName, $content)
    {
        $fs = new Filesystem();

        $fs->dumpFile("$path/$resultClassName.php", $content);
    }
}
