<?php

namespace Spiffy\Mvc;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @covers ::getConfig
     */
    public function testGetConfig()
    {
        $this->assertSame(include __DIR__ . '/../config/config.php', $this->app->getConfig());
    }

    /**
     * @covers ::run, ::bootstrap
     */
    public function testRunFiresEvents()
    {
        $result = '';
        $events = $this->app->events();
        $events->on(MvcEvent::EVENT_BOOTSTRAP, function() use (&$result) {
            $result .= 'bootstrap';
        });
        $events->on(MvcEvent::EVENT_ROUTE, function() use (&$result) {
            $result .= 'route';
        });
        $events->on(MvcEvent::EVENT_DISPATCH, function() use (&$result) {
            $result .= 'dispatch';
        });
        $events->on(MvcEvent::EVENT_RENDER, function() use (&$result) {
            $result .= 'render';
        });
        $events->on(MvcEvent::EVENT_FINISH, function() use (&$result) {
            $result .= 'finish';
        });

        $this->app->run();
        $this->assertSame('bootstraproutedispatchrenderfinish', $result);
    }

    protected function setUp()
    {
        $this->app = $app = new Application();
        $app->getInjector()->nject('request', new Request());
    }
}
