<?php

namespace ReenExe\DesignPatternGenerator;

class NullObjectGenerator extends Generator
{
    public function generate(array $settings): bool
    {
        $class = $settings['class'];

        $reflection = new \ReflectionClass($class);

        if ($reflection->isFinal()) {
            return false;
        }

        return true;
    }
}
