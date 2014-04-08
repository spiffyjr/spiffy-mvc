<?php

namespace Spiffy\Mvc;

use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Mvc\MvcEvent
 */
class MvcEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @covers ::getError, ::getRequest, ::getResponse, ::getRouteMatch, ::getViewModel, ::getResult
     * @covers ::setError, ::setRequest, ::setResponse, ::setRouteMatch, ::setViewModel, ::setResult
     * @dataProvider provider
     */
    public function testMutatorsAndAccessors($name, $value)
    {
        $accessor = 'get' . ucfirst($name);
        $mutator = 'set' . ucfirst($name);

        $this->event->$mutator($value);
        $this->assertSame($value, $this->event->$accessor());
    }

    public function provider()
    {
        return [
            ['error', 'error string'],
            ['request', new Request()],
            ['response', new Response()],
            ['routeMatch', new RouteMatch(new Route('home', '/'))],
            ['viewModel', new ViewModel()],
            ['result', 'result string']
        ];
    }

    /**
     * @covers ::hasError
     */
    public function testHasError()
    {
        $this->assertFalse($this->event->hasError());
        $this->event->setError('error');
        $this->assertTrue($this->event->hasError());
    }

    /**
     * @covers ::__construct, ::getApplication
     */
    public function testGetApplication()
    {
        $this->assertSame($this->app, $this->event->getApplication());
    }

    protected function setUp()
    {
        $this->app = $app = new Application();
        $this->event = new MvcEvent($app);
    }
}
