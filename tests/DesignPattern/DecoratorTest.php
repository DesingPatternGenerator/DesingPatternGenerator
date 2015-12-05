<?php

use ReenExe\DesignPatternGenerator\DecoratorGenerator;

use ReenExe\Fixtures\Source\User;
use ReenExe\Fixtures\Result\Decorator\UserDecorator;

use ReenExe\Fixtures\Source\UserStrict;
use ReenExe\Fixtures\Result\Decorator\UserStrictDecorator;

use ReenExe\Fixtures\Source\UserInterface;
use ReenExe\Fixtures\Result\Decorator\UserInterfaceDecorator;

use ReenExe\Fixtures\Source\AbstractUser;
use ReenExe\Fixtures\Result\Decorator\AbstractUserDecorator;

use ReenExe\Fixtures\Source\AllModifierClass;
use ReenExe\Fixtures\Result\Decorator\AllModifierClassDecorator;

use ReenExe\Fixtures\Source\FinalMethodEntity;
use ReenExe\Fixtures\Result\Decorator\FinalMethodEntityDecorator;

use ReenExe\Fixtures\Result\Decorator\DecoratorGeneratorDecorator;

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
            $generator->generate(
                $classSource ,
                'ReenExe\Fixtures\Result\Decorator',
                FIXTURE_RESULT_PATH . '/Decorator'
            )
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

        yield [
            FinalMethodEntity::class,
            FinalMethodEntityDecorator::class,
        ];

        yield [
            AllModifierClass::class,
            AllModifierClassDecorator::class,
        ];

        yield [
            DecoratorGenerator::class,
            DecoratorGeneratorDecorator::class,
        ];
    }
}
