<?php

namespace ReenExe\Tests\DesignPattern;

use ReflectionClass;

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

use ReenExe\Fixtures\Source\ParameterDefaultValue;
use ReenExe\Fixtures\Result\Decorator\ParameterDefaultValueDecorator;

use ReenExe\Fixtures\Source\VariadicParameterClass;
use ReenExe\Fixtures\Result\Decorator\VariadicParameterClassDecorator;

use ReenExe\Fixtures\Result\Decorator\DecoratorGeneratorDecorator;

use ReenExe\Fixtures\Source\FinalClass;

class DecoratorTest extends AbstractReflectionTest
{
    /**
     * @dataProvider dataProvider
     * @param $sourceClassName
     * @param $resultClassName
     */
    public function test($sourceClassName, $resultClassName)
    {
        $generator = new DecoratorGenerator();

        $this->assertTrue(
            $generator->generate([
                'class' => $sourceClassName,
                'namespace' => 'ReenExe\Fixtures\Result\Decorator',
                'path' => FIXTURE_RESULT_PATH . '/Decorator',
            ])
        );

        $this->assertTrue(class_exists($resultClassName));
        $this->assertTrue(is_subclass_of($resultClassName, $sourceClassName));

        $resultReflectionClass = new ReflectionClass($resultClassName);

        /**
         * Section: Assert same constructor parameter
         */
        $this->assertConstructorType($resultReflectionClass, $sourceClassName);

        /**
         * Section: Assert same public and protected methods
         */
        $sourceReflectionClass = new ReflectionClass($sourceClassName);
        $this->assertSameMethods($sourceReflectionClass, $resultReflectionClass);
    }

    public function testFinalClass()
    {
        $generator = new DecoratorGenerator();

        $this->assertFalse(
            $generator->generate([
                'class' => FinalClass::class,
                'namespace' => 'ReenExe\Fixtures\Result\Decorator',
                'path' => FIXTURE_RESULT_PATH . '/Decorator',
            ])
        );
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
            ParameterDefaultValue::class,
            ParameterDefaultValueDecorator::class,
        ];

        yield [
            ParameterDefaultValue::class,
            ParameterDefaultValueDecorator::class,
        ];

        yield [
            VariadicParameterClass::class,
            VariadicParameterClassDecorator::class,
        ];

        yield [
            DecoratorGenerator::class,
            DecoratorGeneratorDecorator::class,
        ];
    }

    /**
     * @depends test
     */
    public function testInner()
    {
        $user = new User();
        $decorator = new UserDecorator($user);

        $id = 1;
        $user->setId($id);
        $this->assertSame($user->getId(), $id);
        $this->assertSame($decorator->getId(), $id);

        $id = 5;
        $decorator->setId($id);
        $this->assertSame($decorator->getId(), $id);
        $this->assertSame($user->getId(), $id);
    }
}
