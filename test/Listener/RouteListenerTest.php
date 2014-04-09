<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\EventManager;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\Route\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Spiffy\Mvc\Listener\RouteListener
 */
class RouteListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MvcEvent
     */
    protected $e;

    /**
     * @var RouteListener
     */
    protected $l;

    /**
     * @covers ::attach
     */
    public function testAttach()
    {
        $events = new EventManager();
        $l = $this->l;
        $l->attach($events);

        $this->assertCount(1, $events->getEvents());
        $this->assertCount(1, $events->getEvents(MvcEvent::EVENT_ROUTE));
    }

    /**
     * @covers ::onRoute
     */
    public function testOnRouteTriggersErrorOnNoRouteMatch()
    {
        $l = $this->l;
        $e = $this->e;
        $i = $e->getApplication()->getInjector();

        $events = $e->getApplication()->events();
        $result = null;
        $events->on(MvcEvent::EVENT_ROUTE_ERROR, function () use (&$result) {
            $result = 'fired';
        });

        $i->nject('request', new Request());
        $i->nject('router', new Router());

        $l->onRoute($e);
        $this->assertNull($e->getRouteMatch());
        $this->assertSame('fired', $result);
        $this->assertSame(MvcEvent::EVENT_ROUTE_ERROR, $e->getType());
    }

    /**
     * @covers ::onRoute
     */
    public function testOnRoute()
    {
        $l = $this->l;
        $e = $this->e;
        $i = $e->getApplication()->getInjector();

        $request = Request::create('/foo');
        $router = new Router();
        $router->add('foo', '/foo');

        $i->nject('request', $request);
        $i->nject('router', $router);

        $l->onRoute($e);

        $match = $e->getRouteMatch();

        $this->assertInstanceOf('Spiffy\Route\RouteMatch', $match);
        $this->assertSame('/foo', $match->getRoute()->getPath());
    }

    protected function setUp()
    {
        $this->l = new RouteListener();
        $app = new Application();
        $this->e = new MvcEvent($app);
    }
}
