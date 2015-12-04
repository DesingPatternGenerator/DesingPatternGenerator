<?php

use ReenExe\DesignPatternGenerator\DecoratorGenerator;
use ReenExe\Fixtures\Source\User;
use ReenExe\Fixtures\Result\UserDecorator;

class DecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $generator = new DecoratorGenerator();

        $this->assertTrue(
            $generator->generate(User::class , 'ReenExe\Fixtures\Result', FIXTURE_RESULT_PATH)
        );

        $result = new UserDecorator();

        $this->assertTrue($result instanceof User);
    }
}
