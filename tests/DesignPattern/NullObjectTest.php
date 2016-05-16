<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\NullObjectGenerator;

use ReenExe\Fixtures\Source\ViewInterface;
use ReenExe\Fixtures\Result\NullObject\NullObjectViewInterface;

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
    }

    public function testFinalClass()
    {
        $this->assetFalseFinalClassGenerate(new NullObjectGenerator());
    }
}
