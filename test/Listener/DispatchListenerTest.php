<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\EventManager;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Mvc\Listener\DispatchListener
 */
class DispatchListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MvcEvent
     */
    protected $e;

    /**
     * @var DispatchListener
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
        $this->assertCount(1, $events->getEvents(MvcEvent::EVENT_DISPATCH));
    }

    /**
     * @covers ::onDispatch, ::routeNotFound
     */
    public function testOnDispatchWithNoRouteMatch()
    {
        $e = $this->e;
        $l = $this->l;

        $this->assertNull($l->onDispatch($e));
        $this->assertNull($e->getDispatchResult());
    }

    /**
     * @covers ::onDispatch, ::routeNotFound
     */
    public function testOnDispatchWithNoRouteMatchListeners()
    {
        $e = $this->e;
        $l = $this->l;

        $l->onDispatch($e);
        $this->assertNull($e->getDispatchResult());

        $events = $e->getApplication()->events();
        $events->on(MvcEvent::EVENT_ROUTE_ERROR, function() { return 'foo'; });
        $events->on(MvcEvent::EVENT_ROUTE_ERROR, function() { return 'bar'; });

        $l->onDispatch($e);
        $this->assertSame('bar', $e->getDispatchResult());
    }

    /**
     * @covers ::onDispatch, ::controllerNotFound
     */
    public function testOnDispatchWithInvalidController()
    {
        $e = $this->e;
        $l = $this->l;

        $match = new RouteMatch(new Route('home', '/'));
        $e->setRouteMatch($match);

        $this->assertNull($l->onDispatch($e));
        $this->assertNull($e->getDispatchResult());
    }

    /**
     * @covers ::onDispatch, ::controllerNotFound
     */
    public function testOnDispatchWithInvalidControllerListeners()
    {
        $e = $this->e;
        $l = $this->l;

        $events = $e->getApplication()->events();
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, function() { return 'foo'; });
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, function() { return 'bar'; });

        $match = new RouteMatch(new Route('home', '/'));
        $e->setRouteMatch($match);

        $l->onDispatch($e);
        $this->assertSame('bar', $e->getDispatchResult());
    }

    /**
     * @covers ::onDispatch, ::controllerException
     */
    public function testOnDispatchHandlesExceptions()
    {
        $e = $this->e;
        $l = $this->l;
        $i = $e->getApplication()->getInjector();

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('dispatcher');
        $d->add('foo', function() { throw new \RuntimeException(); });

        $match = new RouteMatch(new Route('home', '/'));
        $match->set('controller', 'foo');

        $e->setRouteMatch($match);
        $l->onDispatch($e);
        $this->assertTrue($e->hasError());
        $this->assertSame(MvcEvent::EVENT_DISPATCH_ERROR, $e->getType());
        $this->assertInstanceOf('RuntimeException', $e->get('exception'));
    }

    /**
     * @covers ::onDispatch, ::controllerException
     */
    public function testOnDispatchHandlesExceptionsWithListeners()
    {
        $e = $this->e;
        $l = $this->l;
        $i = $e->getApplication()->getInjector();

        $events = $e->getApplication()->events();
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, function() { return 'foo'; });
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, function() { return 'bar'; });

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('dispatcher');
        $d->add('foo', function() { throw new \RuntimeException(); });

        $match = new RouteMatch(new Route('home', '/'));
        $match->set('controller', 'foo');
        $e->setRouteMatch($match);

        $l->onDispatch($e);
        $this->assertSame('bar', $e->getDispatchResult());
    }

    /**
     * @covers ::onDispatch
     */
    public function testValidOnDispatch()
    {
        $e = $this->e;
        $l = $this->l;
        $i = $e->getApplication()->getInjector();

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('dispatcher');
        $d->add('foo', function() { return 'foo'; });

        $match = new RouteMatch(new Route('home', '/'));
        $match->set('controller', 'foo');

        $e->setRouteMatch($match);

        $l->onDispatch($e);
        $this->assertSame($d->ispatch('foo'), $e->getDispatchResult());
    }

    /**
     * @covers ::onDispatch, ::finish
     */
    public function testOnDispatchSetsResponseIfResultIsResponse()
    {
        $response = new Response();

        $e = $this->e;
        $l = $this->l;
        $i = $e->getApplication()->getInjector();

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('dispatcher');
        $d->add('foo', function() use ($response) { return $response; });

        $match = new RouteMatch(new Route('home', '/'));
        $match->set('controller', 'foo');

        $e->setRouteMatch($match);

        $l->onDispatch($e);
        $this->assertSame($d->ispatch('foo'), $e->getDispatchResult());
        $this->assertSame($response, $e->getResponse());
    }

    protected function setUp()
    {
        $this->l = new DispatchListener();

        $app = new Application();
        $i = $app->getInjector();
        $i->nject('dispatcher', new Dispatcher());

        $this->e = new MvcEvent($app);
    }
}
