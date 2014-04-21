<?php

namespace Spiffy\Mvc;

use Spiffy\Mvc\TestAsset\TestController;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Mvc\AbstractController
 */
class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::notFound
     */
    public function testNotFoundMethodReturnsEmptyViewModel()
    {
        $c = new TestController();
        $this->assertEquals(new ViewModel(), $c->notFound());
    }

    /**
     * @covers ::getInjector, ::assertDispatched
     * @expectedException \Spiffy\Mvc\Exception\MissingMvcEventException
     * @expectedExceptionMessage Failed to dispatch: no MvcEvent available in params.
     */
    public function testOnDispatchThrowsExceptionForMissingMvcEvent()
    {
        $c = new TestController();
        $c->dispatch([]);
    }

    /**
     * @covers ::getInjector, ::assertDispatched
     * @expectedException \Spiffy\Mvc\Exception\MissingMvcEventException
     * @expectedExceptionMessage MvcEvent is not available: has the controller been dispatched?
     */
    public function testGetInjectorThrowsExceptionWithNoDispatch()
    {
        $c = new TestController();
        $c->getInjector();
    }

    /**
     * @covers ::getResponse, ::assertDispatched
     * @expectedException \Spiffy\Mvc\Exception\MissingMvcEventException
     * @expectedExceptionMessage MvcEvent is not available: has the controller been dispatched?
     */
    public function testGetResponseThrowsExceptionWithNoDispatch()
    {
        $c = new TestController();
        $c->getResponse();
    }

    /**
     * @covers ::getRequest, ::assertDispatched
     * @expectedException \Spiffy\Mvc\Exception\MissingMvcEventException
     * @expectedExceptionMessage MvcEvent is not available: has the controller been dispatched?
     */
    public function testGetRequestThrowsExceptionWithNoDispatch()
    {
        $c = new TestController();
        $c->getRequest();
    }

    /**
     * @covers ::getInjector
     */
    public function testGetInjector()
    {
        $event = new MvcEvent(new Application());

        $c = new TestController();
        $c->dispatch(['event' => $event]);

        $this->assertSame($event->getApplication()->getInjector(), $c->getInjector());
    }

    /**
     * @covers ::getResponse, ::getRequest
     */
    public function testGetResponseRequest()
    {
        $response = new Response();
        $request = new Request();

        $event = new MvcEvent(new Application());
        $event->setResponse($response);
        $event->setRequest($request);

        $c = new TestController();
        $c->dispatch(['event' => $event]);

        $this->assertSame($response, $c->getResponse());
        $this->assertSame($request, $c->getRequest());
    }

    /**
     * @covers ::dispatch
     */
    public function testDispatchFiresDispatchErrorOnMissingAction()
    {
        $result = 'error';
        $app = new Application();
        $events = $app->events();
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, function() use ($result) {
            return $result;
        });
        $c = new TestController();

        $params = ['event' => new MvcEvent($app), 'action' => 'doesnotexist'];
        $this->assertSame($result, $c->dispatch($params));
    }

    /**
     * @covers ::dispatch, ::normalize
     */
    public function testDispatchNormalizesActions()
    {
        $c = new TestController();
        $params = ['action' => 'camel-case', 'event' => new MvcEvent(new Application())];
        $this->assertSame('camelCase', $c->dispatch($params));
    }

    /**
     * @covers ::dispatch
     */
    public function testDefaultActionIsIndex()
    {
        $c = new TestController();
        $this->assertSame('index', $c->dispatch(['event' => new MvcEvent(new Application())]));
    }
}
