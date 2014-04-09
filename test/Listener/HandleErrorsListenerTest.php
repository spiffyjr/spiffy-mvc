<?php

namespace Spiffy\Mvc\Listener;
use Spiffy\Event\EventManager;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Mvc\TestAsset\TestStrategy;
use Spiffy\Mvc\View\ViewManager;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;

/**
 * @coversDefaultClass \Spiffy\Mvc\Listener\HandleErrorsListener
 */
class HandleErrorsListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MvcEvent
     */
    protected $e;

    /**
     * @var HandleErrorsListener
     */
    protected $l;

    /**
     * @covers ::attach
     */
    public function testAttach()
    {
        $events = new EventManager();
        $l = new HandleErrorsListener();
        $l->attach($events);

        $this->assertCount(3, $events->getEvents());
        $this->assertCount(2, $events->getEvents(MvcEvent::EVENT_DISPATCH_ERROR));
        $this->assertCount(1, $events->getEvents(MvcEvent::EVENT_ROUTE_ERROR));
        $this->assertCount(1, $events->getEvents(MvcEvent::EVENT_RENDER_ERROR));
    }

    /**
     * @covers ::handleExceptions
     */
    public function testHandleExceptiosnReturnsEarlyIfErrorIsNotException()
    {
        $l = $this->l;
        $e = $this->e;

        $this->assertNull($l->handleExceptions($e));
        $this->assertNull($e->getResult());
    }

    /**
     * @covers ::handleErrors
     */
    public function testHandleExceptiosnReturnsEarlyIfErrorIsNotCorrectError()
    {
        $l = $this->l;
        $e = $this->e;

        $e->setError(MvcEvent::ERROR_EXCEPTION);
        $this->assertNull($l->handleErrors($e));

        $e->setError(MvcEvent::ERROR_NO_ROUTE);
        $this->assertNotNull($l->handleErrors($e));
    }

    /**
     * @covers ::handleExceptions
     */
    public function testHandleExceptions()
    {
        $l = $this->l;
        $e = $this->e;
        $e->setError(MvcEvent::ERROR_EXCEPTION);

        $i = $e->getApplication()->getInjector();
        /** @var \Spiffy\Mvc\View\ViewManager $vm */
        $vm = $i->nvoke('view_manager');

        $result = $l->handleExceptions($e);
        $model = $e->getViewModel();

        $this->assertSame($result, $model);
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
        $this->assertSame($model->getTemplate(), $vm->getErrorTemplate());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $e->getResponse());
        $this->assertSame(500, $e->getResponse()->getStatusCode());
        $this->assertSame($model, $e->getResult());
    }

    /**
     * @covers ::handleErrors
     */
    public function testHandleErrorsWithNoController()
    {
        $l = $this->l;
        $e = $this->e;

        $rm = new RouteMatch(new Route('home', '/'));

        $e->setError(MvcEvent::ERROR_NO_CONTROLLER);
        $e->setRouteMatch($rm);

        $i = $e->getApplication()->getInjector();
        /** @var \Spiffy\Mvc\View\ViewManager $vm */
        $vm = $i->nvoke('view_manager');

        $result = $l->handleErrors($e);
        $model = $e->getViewModel();

        $this->assertSame($result, $model);
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
        $this->assertSame($model->getTemplate(), $vm->getNotFoundTemplate());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $e->getResponse());
        $this->assertSame(404, $e->getResponse()->getStatusCode());
        $this->assertSame($model, $e->getResult());
        $this->assertArrayHasKey('controller', $model->getVariables());
    }

    /**
     * @covers ::handleErrors
     */
    public function testHandleErrorsWithNoAction()
    {
        $l = $this->l;
        $e = $this->e;

        $rm = new RouteMatch(new Route('home', '/'));

        $e->setError(MvcEvent::ERROR_NO_ACTION);
        $e->setRouteMatch($rm);

        $i = $e->getApplication()->getInjector();
        /** @var \Spiffy\Mvc\View\ViewManager $vm */
        $vm = $i->nvoke('view_manager');

        $result = $l->handleErrors($e);
        $model = $e->getViewModel();

        $this->assertSame($result, $model);
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
        $this->assertSame($model->getTemplate(), $vm->getNotFoundTemplate());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $e->getResponse());
        $this->assertSame(404, $e->getResponse()->getStatusCode());
        $this->assertSame($model, $e->getResult());
        $this->assertArrayHasKey('controller', $model->getVariables());
        $this->assertArrayHasKey('action', $model->getVariables());
    }

    /**
     * @covers ::handleErrors
     */
    public function testHandleErrorsWithNoRoute()
    {
        $l = $this->l;
        $e = $this->e;

        $e->setError(MvcEvent::ERROR_NO_ROUTE);

        $i = $e->getApplication()->getInjector();
        /** @var \Spiffy\Mvc\View\ViewManager $vm */
        $vm = $i->nvoke('view_manager');

        $result = $l->handleErrors($e);
        $model = $e->getViewModel();

        $this->assertSame($result, $model);
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
        $this->assertSame($model->getTemplate(), $vm->getNotFoundTemplate());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $e->getResponse());
        $this->assertSame(404, $e->getResponse()->getStatusCode());
        $this->assertSame($model, $e->getResult());
        $this->assertArrayNotHasKey('controller', $model->getVariables());
        $this->assertArrayNotHasKey('action', $model->getVariables());
    }

    protected function setUp()
    {
        $this->l = new HandleErrorsListener();

        $app = new Application();
        $i = $app->getInjector();
        $i->nject('view_manager', new ViewManager(new TestStrategy()));

        $this->e = new MvcEvent($app);
    }
}
