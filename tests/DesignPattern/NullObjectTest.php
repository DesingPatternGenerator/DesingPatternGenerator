<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\NullObjectGenerator;

use ReenExe\Fixtures\Source\ViewInterface;
use ReenExe\Fixtures\Result\NullObject\NullViewInterface;

class NullObjectTestCommon extends AbstractCommonReflectionTest
{
    public function test()
    {
        $generator = new NullObjectGenerator();

        $this->assertTrue(
            $generator->generate([
                'class' => ViewInterface::class,
                'namespace' => 'ReenExe\Fixtures\Result\NullObject',
                'path' => FIXTURE_RESULT_PATH . '/NullObject',
            ])
        );

        $this->assertTrue(class_exists(NullViewInterface::class));
    }

    public function testFinalClass()
    {
        $this->assetFalseFinalClassGenerate(new NullObjectGenerator());
    }
}
