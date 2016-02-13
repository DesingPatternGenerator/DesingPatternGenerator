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
     * @param array $methods
     */
    public function test($sourceClassName, array $methods)
    {
        $generator = new CompositeGenerator();

        $generator->generate([
            'class' => $sourceClassName,
            'namespace' => 'ReenExe\Fixtures\Result\Composite',
            'path' => FIXTURE_RESULT_PATH . '/Composite',
            'methods' => $methods
        ]);
    }

    public function dataProvider()
    {
        $methods = ['add'];

        yield [
            View::class,
            $methods,
        ];

        $methods = ['add'];

        yield [
            ViewInterface::class,
            $methods,
        ];
    }
}
