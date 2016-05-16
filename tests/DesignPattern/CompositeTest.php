<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\CompositeGenerator;

use ReenExe\Fixtures\Source\View;
use ReenExe\Fixtures\Source\ViewInterface;

use ReenExe\Fixtures\Result\Composite\ViewComposite;
use ReenExe\Fixtures\Result\Composite\ViewInterfaceComposite;

class CompositeTestCommon extends AbstractCommonReflectionTest
{
    /**
     * @var CompositeGenerator
     */
    private $generator;

    protected function setUp()
    {
        $this->generator = new CompositeGenerator();
    }

    /**
     * @dataProvider viewDataProvider
     * @param $sourceClassName
     */
    public function testViewComposite($sourceClassName, $compositeClass)
    {
        $this->assertTrue($this->generate($sourceClassName));
        /* @var $composite ViewInterfaceComposite|ViewComposite */
        $composite = new $compositeClass();

        $mockBuilder = $this->getMockBuilder($sourceClassName);
        $view = $mockBuilder->getMock();
        $view->expects($this->exactly(2))->method('render');

        $composite->add($view);
        $composite->render();

        $view = $mockBuilder->getMock();
        $view->expects($this->exactly(1))->method('render');

        $composite->add($view);
        $composite->render();
    }

    public function viewDataProvider()
    {
        yield [View::class, ViewComposite::class];
        yield [ViewInterface::class, ViewInterfaceComposite::class];
    }

    private function generate($sourceClassName)
    {
        return $this->generator->generate([
            'class' => $sourceClassName,
            'namespace' => 'ReenExe\Fixtures\Result\Composite',
            'path' => FIXTURE_RESULT_PATH . '/Composite',
        ]);
    }
}
