<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\NullObjectGenerator;

class NullObjectTestCommon extends AbstractCommonReflectionTest
{
    public function testFinalClass()
    {
        $this->assetFalseFinalClassGenerate(new NullObjectGenerator());
    }
}
