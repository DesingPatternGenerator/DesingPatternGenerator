<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\NullObjectGenerator;
use ReenExe\Fixtures\Source\FinalClass;

class NullObjectTest extends AbstractReflectionTest
{
    public function testFinalClass()
    {
        $generator = new NullObjectGenerator();

        $this->assertFalse(
            $generator->generate([
                'class' => FinalClass::class
            ])
        );
    }
}
