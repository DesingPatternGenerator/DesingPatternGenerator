<?php

namespace ReenExe\Tests\DesignPattern;

use ReenExe\DesignPatternGenerator\CompositeGenerator;

use ReenExe\Fixtures\Source\View;
use ReenExe\Fixtures\Source\ViewInterface;

use ReenExe\Fixtures\Result\Composite\ViewComposite;
use ReenExe\Fixtures\Result\Composite\ViewInterfaceComposite;

class CompositeTest extends AbstractReflectionTest
{
    /**
     * @var CompositeGenerator
     */
    private $generator;

    protected function setUp()
    {
        $this->generator = new CompositeGenerator();
    }

    public function testViewComposite()
    {
        $this->assertTrue($this->generate(View::class));
    }

    public function testViewInterfaceComposite()
    {
        $this->assertTrue($this->generate(ViewInterface::class));
    }

    private function generate($sourceClassName)
    {
        $this->generator->generate([
            'class' => $sourceClassName,
            'namespace' => 'ReenExe\Fixtures\Result\Composite',
            'path' => FIXTURE_RESULT_PATH . '/Composite',
        ]);
    }
}
