<?php

use PHPUnit\Framework\TestCase;

class HelloWorldUnitTest extends TestCase
{
    public function testHelloWorld()
    {
        $this->assertEquals('Hello, World!', 'Hello, World!');
    }
}