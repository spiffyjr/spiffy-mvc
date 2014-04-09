<?php

namespace Spiffy\Mvc\View;
use Spiffy\View\TwigRenderer;
use Spiffy\View\TwigResolver;

/**
 * @coversDefaultClass \Spiffy\Mvc\View\TwigStrategy
 */
class TwigStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigRenderer
     */
    protected $r;

    /**
     * @var TwigStrategy
     */
    protected $s;

    /**
     * @covers ::render, ::__construct
     */
    public function testRenderProxiesToRenderer()
    {
        $this->assertSame($this->r->render('test'), $this->s->render('test'));
    }

    public function testCanRenderAlwaysReturnsTrue()
    {
        $this->assertTrue($this->s->canRender('a'));
    }

    protected function setUp()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_String());
        $resolver = new TwigResolver($twig);
        $this->r = $r = new TwigRenderer($twig, $resolver);

        $this->s = $s = new TwigStrategy($r, $resolver);
    }
}
