<?php

use ReenExe\DesignPatternGenerator\DecoratorGenerator;
use ReenExe\Fixtures\Source\User;
use ReenExe\Fixtures\Result\UserDecorator;
use ReenExe\Fixtures\Source\UserStrict;
use ReenExe\Fixtures\Result\UserStrictDecorator;
use ReenExe\Fixtures\Source\UserInterface;
use ReenExe\Fixtures\Result\UserInterfaceDecorator;
use ReenExe\Fixtures\Source\AbstractUser;
use ReenExe\Fixtures\Result\AbstractUserDecorator;

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

        $reflectionResultClass = new \ReflectionClass($classResult);

        $constructorReflectionMethod = $reflectionResultClass->getConstructor();

        $this->assertTrue((bool) $constructorReflectionMethod);

        $constructorReflectionParameters = $constructorReflectionMethod->getParameters();

        $this->assertTrue(count($constructorReflectionParameters) === 1);
        /* @var $constructorReflectionParameter \ReflectionParameter */
        $constructorReflectionParameter = current($constructorReflectionParameters);

        /* @var $constructorReflectionType \ReflectionType */
        $constructorReflectionType = $constructorReflectionParameter->getType();
        $this->assertTrue((bool) $constructorReflectionType);

        $this->assertSame((string) $constructorReflectionType, $classSource);
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

        yield [
            UserInterface::class,
            UserInterfaceDecorator::class,
        ];

        yield [
            AbstractUser::class,
            AbstractUserDecorator::class,
        ];
    }
}
