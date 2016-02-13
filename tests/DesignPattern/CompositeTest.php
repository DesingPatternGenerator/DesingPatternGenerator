<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\CompositeGenerator;

use ReenExe\Fixtures\Source\View;
use ReenExe\Fixtures\Source\ViewInterface;

class CompositeTest extends AbstractReflectionTest
{
    /**
     * @dataProvider dataProvider
     * @param $sourceClassName
     */
    public function testViewComposite($sourceClassName)
    {
        $generator = new CompositeGenerator();

        $generator->generate([
            'class' => $sourceClassName,
            'namespace' => 'ReenExe\Fixtures\Result\Composite',
            'path' => FIXTURE_RESULT_PATH . '/Composite',
        ]);
    }

    public function dataProvider()
    {
        $methods = [];

        yield [
            View::class
        ];

        yield [
            ViewInterface::class
        ];
    }
}
