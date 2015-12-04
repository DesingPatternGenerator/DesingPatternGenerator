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

        $result = $this->getResultString([
            ':namespace:' => "namespace $namespace;",
            ':use:' => "use $class;",
            ':header:' => "class $resultClassName extends $sourceClassName",
            ':body:' => '',
        ]);

        $fs->dumpFile("$path/$resultClassName.php", $result);

        return true;
    }
}
