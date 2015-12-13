<?php

namespace ReenExe\DesignPatternGenerator;

class DecoratorGenerator extends Generator
{
    /**
     * @param array $settings
     * @return bool
     */
    public function generate(array $settings): bool
    {
        $class = $settings['class'];
        $namespace = $settings['namespace'];
        $path = $settings['path'];

        $reflection = new \ReflectionClass($class);

        if ($reflection->isFinal()) {
            return false;
        }

        $this
            ->clearUse()
            ->addUseClass($class);

        $sourceClassName = $reflection->getShortName();
        $resultClassName = $sourceClassName . 'Decorator';

        $sourceClassMethods = $this->getClassMethods($reflection);

        if (empty($sourceClassMethods)) {
            return false;
        }

        $body = [
            $this->getResultPropertyString([
                ':modifiers:' => 'private',
                ':name:' => 'subject',
            ]),

            $this->getResultMethodString([
                ':modifiers:' => 'public',
                ':name:' => '__construct',
                ':parameters:' => $sourceClassName . ' $subject',
                ':body:' => '$this->subject = $subject;',
            ])
        ];

        array_push($body, ...$sourceClassMethods);

        $result = $this->getResultClassString([
            ':namespace:' => "namespace $namespace;",
            ':header:' => "class $resultClassName {$this->getBehavior($reflection)} $sourceClassName",
            ':body:' => join(PHP_EOL, $body),
        ]);

        $this->store($path, $resultClassName, $result);

        return true;
    }

    protected function getMethodBody(\ReflectionMethod $reflectionMethod)
    {
        static $template = 'return $this->subject->:method:(:paraeters:);';

        $parameters = [];

        foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
            $parameters[] = '$' . $reflectionParameter->getName();
        }

        return strtr($template, [
            ':method:' => $reflectionMethod->getName(),
            ':paraeters:' => join(', ', $parameters),
        ]);
    }
}
