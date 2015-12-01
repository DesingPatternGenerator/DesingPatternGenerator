<?php

use ReenExe\DesignPatternGenerator\DecoratorGenerator;
use ReenExe\Fixtures\User;

class DecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $generator = new DecoratorGenerator();

        $this->assertSame($generator->generate(User::class), User::class);
    }
}
