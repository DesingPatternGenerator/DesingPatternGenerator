<?php

namespace ReenExe\DesignPatternGenerator;

class CompositeGenerator extends Generator
{
    public function generate(array $settings): bool
    {
        $class = $settings['class'];

        $reflection = new \ReflectionClass($class);

        if ($reflection->isFinal()) {
            return false;
        }

        $this
            ->clearUse()
            ->addUseClass($class);

        $sourceClassMethods = $this->getClassMethods($reflection);

        if (empty($sourceClassMethods)) {
            return false;
        }

        $sourceClassName = $reflection->getShortName();
        $namespace = $settings['namespace'];
        $path = $settings['path'];
        $resultClassName = $sourceClassName . 'Composite';

        $body = [
            $this->getResultPropertyString([
                ':comment:' => $this->getElementListTypeHint($sourceClassName),
                ':modifiers:' => 'private',
                ':name:' => 'elements',
            ]),
        ];

        $methods = $this->getClassMethods($reflection);

        $compositeMethodList = $settings['methods'] ?? [];
        $addElementMethodName = $compositeMethodList['add'] ?? 'add';
        $methods[] = $this->getResultMethodString([
            ':modifiers:' => 'public',
            ':name:' => $addElementMethodName,
            ':parameters:' => $sourceClassName . ' $element',
            ':body:' => '$this->elements[] = $element;'
        ]);

        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':header:' => "class $resultClassName {$this->getBehavior($reflection)} $sourceClassName",
            ':body:' => join(PHP_EOL, array_merge($body, $methods)),
        ]);

        $this->store($path, $resultClassName, $result);

        return true;
    }

    protected function getMethodBody(\ReflectionMethod $reflectionMethod)
    {
        $each =  <<<'PHP'

        foreach ($this->elements as $element) {
            $element->%s();
        }

PHP;
        return sprintf($each, $reflectionMethod->getName());
    }

    private function getElementListTypeHint($class)
    {
        return <<<COMMENT

    /**
     * @var {$class}[]|array
     */
COMMENT;
    }
}
