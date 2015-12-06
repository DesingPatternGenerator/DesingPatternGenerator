<?php

namespace ReenExe\Tests\DesignPattern;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;
use PHPUnit_Framework_TestCase;

abstract class AbstractReflectionTest extends PHPUnit_Framework_TestCase
{
    protected function assertConstructorType(ReflectionClass $reflectionResultClass, $sourceClassName)
    {
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
    }

    /**
     * @param ReflectionClass $reflectionSourceClass
     * @param ReflectionClass $reflectionResultClass
     */
    protected function assertSameMethods(
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
    protected function assertSameParameters(
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
    protected function assertSameKeys(array $source, array $expected)
    {
        $this->assertSame(array_keys($source), array_keys($expected));
    }

    protected function assertSameParameter(ReflectionParameter $source, ReflectionParameter $expected)
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
    protected function assertSameReflectionType($source, $expected)
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
    protected function getReflectionMethodMap(ReflectionClass $reflectionClass)
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
    protected function getReflectionParameterMap(ReflectionMethod $method)
    {
        $map = [];

        foreach ($method->getParameters() as $parameter) {
            $map[$parameter->getName()] = $parameter;
        }

        return $map;
    }

    protected function getCompareModifiers()
    {
        return ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PUBLIC;
    }
}
