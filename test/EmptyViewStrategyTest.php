<?php

namespace Spiffy\Mvc;

/**
 * @coversDefaultClass \Spiffy\Mvc\EmptyViewStrategy
 */
class EmptyViewStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::canRender
     */
    public function testCanRender()
    {
        $s = new EmptyViewStrategy();
        $this->assertTrue($s->canRender('foo/bar'));
    }

    /**
     * @covers ::render
     */
    public function testRender()
    {
        $s = new EmptyViewStrategy();
        $result = $s->render('foo/bar');
        $this->assertSame('EmptyViewStrategy: no renderer is available for foo/bar', $result);
    }
}
