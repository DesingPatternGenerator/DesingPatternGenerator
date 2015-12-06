<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\AdapterGenerator;

use ReenExe\Fixtures\Source\UserRepository;
use ReenExe\Fixtures\Source\Adapter\PagerAdapter;
use ReenExe\Fixtures\Result\Adapter\UserRepositoryAdapter;

class AdapterTest extends AbstractReflectionTest
{
    /**
     * @dataProvider dataProvider
     * @param $sourceClassName
     * @param $adapterClassName
     * @param $resultClassName
     */
    public function test($sourceClassName, $adapterClassName , $resultClassName)
    {
        $generator = new AdapterGenerator();

        $this->assertTrue(
            $generator->generate([
                'class' => $sourceClassName,
                'adapter' => $adapterClassName,
                'namespace' => 'ReenExe\Fixtures\Result\Adapter',
                'path' => FIXTURE_RESULT_PATH . '/Adapter',
            ])
        );

        $resultReflectionClass = new \ReflectionClass($resultClassName);
        /**
         * Section: Assert same constructor parameter
         */
        $this->assertConstructorType($resultReflectionClass, $sourceClassName);

        $adapterReflectionClass = new \ReflectionClass($adapterClassName);

        $this->assertSameMethods(
            $adapterReflectionClass,
            $resultReflectionClass
        );
    }

    public function dataProvider()
    {
        yield [
            UserRepository::class,
            PagerAdapter::class,
            UserRepositoryAdapter::class,
        ];
    }
}
