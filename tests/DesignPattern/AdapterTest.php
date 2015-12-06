<?php

use ReenExe\DesignPatternGenerator\AdapterGenerator;

use ReenExe\Fixtures\Source\UserRepository;
use ReenExe\Fixtures\Source\Adapter\PagerAdapter;
use ReenExe\Fixtures\Result\Adapter\UserRepositoryAdapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
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
