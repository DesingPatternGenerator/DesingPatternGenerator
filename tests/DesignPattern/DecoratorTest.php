<?php

use ReenExe\DesignPatternGenerator\DecoratorGenerator;
use ReenExe\Fixtures\Source\User;
use ReenExe\Fixtures\Result\UserDecorator;
use ReenExe\Fixtures\Source\UserStrict;
use ReenExe\Fixtures\Result\UserStrictDecorator;

class DecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProvider
     * @param $classSource
     * @param $classResult
     */
    public function test($classSource, $classResult)
    {
        $generator = new DecoratorGenerator();

        $this->assertTrue(
            $generator->generate($classSource , 'ReenExe\Fixtures\Result', FIXTURE_RESULT_PATH)
        );

        $this->assertTrue(is_subclass_of($classResult, $classSource));
        $classResultInstance = new $classResult(new $classSource());
        $this->assertTrue($classResultInstance instanceof $classSource);
    }

    public function dataProvider()
    {
        yield [
            User::class,
            UserDecorator::class,
        ];

        yield [
            UserStrict::class,
            UserStrictDecorator::class,
        ];
    }
}
