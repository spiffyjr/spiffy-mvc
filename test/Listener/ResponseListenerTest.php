<?php

namespace Spiffy\Mvc\Plugin;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\EventManager;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Mvc\Plugin\ResponsePlugin
 */
class ResponsePluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MvcEvent
     */
    protected $e;

    /**
     * @var ResponsePlugin
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
        $this->assertCount(1, $events->getEvents(MvcEvent::EVENT_FINISH));
    }

    /**
     * @covers ::sendResponse
     */
    public function testSendResponseCreatesEmptyResponse()
    {
        $l = $this->l;
        $e = $this->e;
        $e->setRenderResult('foo');

        ob_start();
        $responseRessult = $l->sendResponse($e);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertSame('foo', $result);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $responseRessult);
    }

    /**
     * @covers ::sendResponse
     */
    public function testSendResponse()
    {
        $l = $this->l;
        $e = $this->e;
        $e->setRenderResult('foo');

        $response = new Response();
        $e->setResponse($response);

        ob_start();
        $responseRessult = $l->sendResponse($e);
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertSame('foo', $result);
        $this->assertSame($response, $responseRessult);
    }

    protected function setUp()
    {
        $this->l = new ResponsePlugin();

        $app = new Application();
        $i = $app->getInjector();
        $i->nject('dispatcher', new Dispatcher());

        $this->e = new MvcEvent($app);
    }
}
