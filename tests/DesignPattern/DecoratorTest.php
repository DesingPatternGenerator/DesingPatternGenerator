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

use ReenExe\Fixtures\Source\ParameterDefaultValue;
use ReenExe\Fixtures\Result\Decorator\ParameterDefaultValueDecorator;

use ReenExe\Fixtures\Source\VariadicParameterClass;
use ReenExe\Fixtures\Result\Decorator\VariadicParameterClassDecorator;

use ReenExe\Fixtures\Result\Decorator\DecoratorGeneratorDecorator;

class DecoratorTest extends PHPUnit_Framework_TestCase
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

        $reflectionResultClass = new ReflectionClass($resultClassName);

        /**
         * Section: Assert same constructor parameter
         */
        $constructorReflectionMethod = $reflectionResultClass->getConstructor();

        $this->assertTrue((bool) $constructorReflectionMethod);

        $constructorReflectionParameters = $constructorReflectionMethod->getParameters();

        $this->assertTrue(count($constructorReflectionParameters) === 1);
        /* @var $constructorReflectionParameter ReflectionParameter */
        $constructorReflectionParameter = current($constructorReflectionParameters);

        /* @var $constructorReflectionType ReflectionType */
        $constructorReflectionType = $constructorReflectionParameter->getType();
        $this->assertTrue((bool) $constructorReflectionType);

        $this->assertSame((string) $constructorReflectionType, $sourceClassName);

        /**
         * Section: Assert same public and protected methods
         */
        $reflectionSourceClass = new ReflectionClass($sourceClassName);
        $this->assertSameMethods($reflectionSourceClass, $reflectionResultClass);
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
     * @param ReflectionClass $reflectionSourceClass
     * @param ReflectionClass $reflectionResultClass
     */
    private function assertSameMethods(
        ReflectionClass $reflectionSourceClass,
        ReflectionClass $reflectionResultClass
    ) {
        $sourceClassMethods = $this->getReflectionMethodMap($reflectionSourceClass);
        $resultClassMethods = $this->getReflectionMethodMap($reflectionResultClass);

        $this->assertSameKeys($sourceClassMethods, $resultClassMethods);

        $compareModifiers = $this->getCompareModifiers();
        foreach ($sourceClassMethods as $methodName => $sourceMethod) {
            $expectedMethod = $resultClassMethods[$methodName];

            $this->assertSame(
                $sourceMethod->getModifiers() & $compareModifiers,
                $expectedMethod->getModifiers() & $compareModifiers
            );

            $this->assertSameReflectionType(
                $sourceMethod->getReturnType(),
                $expectedMethod->getReturnType()
            );

            $this->assertSameParameters($sourceMethod, $expectedMethod);
        }
    }

    /**
     * @param ReflectionMethod $sourceMethod
     * @param ReflectionMethod $expectedMethod
     */
    private function assertSameParameters(
        ReflectionMethod $sourceMethod,
        ReflectionMethod $expectedMethod
    ) {
        $sourceMethodParameterMap = $this->getReflectionParameterMap($sourceMethod);
        $expectedMethodParameterMap = $this->getReflectionParameterMap($expectedMethod);

        $this->assertSameKeys($sourceMethodParameterMap, $expectedMethodParameterMap);

        /**
         * Short example of same logic
            array_map([$this, 'assertSameParameter'], $sourceMethodParameterMap, $expectedMethodParameterMap);
         */

        foreach ($sourceMethodParameterMap as $name => $sourceParameter) {
            $expectParameter = $expectedMethodParameterMap[$name];

            $this->assertSameParameter($sourceParameter, $expectParameter);
        }
    }

    /**
     * @param array $source
     * @param array $expected
     */
    private function assertSameKeys(array $source, array $expected)
    {
        $this->assertSame(array_keys($source), array_keys($expected));
    }

    private function assertSameParameter(ReflectionParameter $source, ReflectionParameter $expected)
    {
        $this->assertSame(
            $source->getName(),
            $expected->getName()
        );

        $this->assertSameReflectionType(
            $source->getType(),
            $expected->getType()
        );

        $this->assertSame(
            $source->isDefaultValueAvailable(),
            $expected->isDefaultValueAvailable()
        );

        if ($source->isDefaultValueAvailable()) {
            $this->assertSame(
                $source->getDefaultValue(),
                $expected->getDefaultValue()
            );

            $this->assertSame(
                $source->isDefaultValueConstant(),
                $expected->isDefaultValueConstant()
            );

            if ($source->isDefaultValueConstant()) {
                $this->assertSame(
                    $source->getDefaultValueConstantName(),
                    $expected->getDefaultValueConstantName()
                );
            }
        }
    }

    /**
     * @param ReflectionType|null $source
     * @param ReflectionType|null $expected
     */
    private function assertSameReflectionType($source, $expected)
    {
        $this->assertTrue(
            ($source === null && $expected === null)
            ||
            ((string)$source === (string)$expected)
        );
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return ReflectionMethod[]
     */
    private function getReflectionMethodMap(ReflectionClass $reflectionClass)
    {
        $methods = $reflectionClass->getMethods($this->getCompareModifiers());

        $map = [];

        foreach ($methods as $method) {
            if ($method->isConstructor()) continue;

            $map[$method->getName()] = $method;
        }

        ksort($map);

        return $map;
    }

    /**
     * @param ReflectionMethod $method
     * @return ReflectionParameter[]
     */
    private function getReflectionParameterMap(ReflectionMethod $method)
    {
        $map = [];

        foreach ($method->getParameters() as $parameter) {
            $map[$parameter->getName()] = $parameter;
        }

        return $map;
    }

    private function getCompareModifiers()
    {
        return ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PUBLIC;
    }
}
