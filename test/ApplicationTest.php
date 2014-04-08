<?php

namespace Spiffy\Mvc;

use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Mvc\Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @covers ::getEvent
     */
    public function testGetEventIsLazyLoaded()
    {
        $app = $this->app;
        $i = $app->getInjector();
        $this->assertInstanceOf('Spiffy\Mvc\MvcEvent', $app->getEvent());
        $this->assertSame($i->nvoke('request'), $app->getEvent()->getRequest());
    }

    /**
     * @covers ::getInjector
     */
    public function testGetInjectorIsLazyLoaded()
    {
        $app = $this->app;
        $this->assertInstanceOf('Spiffy\Inject\Injector', $app->getInjector());
    }

    protected function setUp()
    {
        $this->app = $app = new Application();
        $app->getInjector()->nject('request', new Request());
    }
}
