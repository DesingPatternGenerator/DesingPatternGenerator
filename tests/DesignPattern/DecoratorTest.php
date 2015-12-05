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
     * @param $sourceClassName
     * @param $resultClassName
     */
    public function test($sourceClassName, $resultClassName)
    {
        $generator = new DecoratorGenerator();

        $this->assertTrue(
            $generator->generate(
                $sourceClassName ,
                'ReenExe\Fixtures\Result\Decorator',
                FIXTURE_RESULT_PATH . '/Decorator'
            )
        );

        $this->assertTrue(class_exists($resultClassName));
        $this->assertTrue(is_subclass_of($resultClassName, $sourceClassName));

        $reflectionResultClass = new \ReflectionClass($resultClassName);

        /**
         * Section: Assert same constructor parameter
         */
        $constructorReflectionMethod = $reflectionResultClass->getConstructor();

        $this->assertTrue((bool) $constructorReflectionMethod);

        $constructorReflectionParameters = $constructorReflectionMethod->getParameters();

        $this->assertTrue(count($constructorReflectionParameters) === 1);
        /* @var $constructorReflectionParameter \ReflectionParameter */
        $constructorReflectionParameter = current($constructorReflectionParameters);

        /* @var $constructorReflectionType \ReflectionType */
        $constructorReflectionType = $constructorReflectionParameter->getType();
        $this->assertTrue((bool) $constructorReflectionType);

        $this->assertSame((string) $constructorReflectionType, $sourceClassName);

        /**
         * Section: Assert same public and protected methods
         */
        $reflectionSourceClass = new \ReflectionClass($sourceClassName);

        $sourceClassMethods = $this->getOpenMethods($reflectionSourceClass);
        $resultClassMethods = $this->getOpenMethods($reflectionResultClass);

        $this->assertSameMethods($sourceClassMethods, $resultClassMethods);
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

    /**
     * @param ReflectionClass $reflectionClass
     * @return \ReflectionMethod[]
     */
    private function getOpenMethods(\ReflectionClass $reflectionClass)
    {
        $methods = $reflectionClass->getMethods($this->getCompareModifiers());

        $result = [];

        foreach ($methods as $method) {
            if ($method->isConstructor()) continue;

            $result[$method->getName()] = $method;
        }

        ksort($result);

        return $result;
    }

    /**
     * @param \ReflectionMethod[] $source
     * @param \ReflectionMethod[] $expected
     */
    private function assertSameMethods(array $source, array $expected)
    {
        $this->assertSame(array_keys($source), array_keys($expected));

        $compareModifiers = $this->getCompareModifiers();
        foreach ($source as $methodName => $sourceMethod) {
            $expectedMethod = $expected[$methodName];

            $this->assertSame(
                $sourceMethod->getModifiers() & $compareModifiers,
                $expectedMethod->getModifiers() & $compareModifiers
            );
        }
    }

    private function getCompareModifiers()
    {
        return \ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PUBLIC;
    }
}
