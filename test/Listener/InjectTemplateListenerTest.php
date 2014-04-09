<?php

namespace Spiffy\Mvc\Listener;
use Spiffy\Event\EventManager;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\View\ViewModel;

/**
 * @coversDefaultClass \Spiffy\Mvc\Listener\InjectTemplateListener
 */
class InjectTemplateListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MvcEvent
     */
    protected $e;

    /**
     * @var InjectTemplateListener
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
     * @covers ::createViewTemplate
     */
    public function testCreateViewTemplateReturnsEarlyWithTemplate()
    {
        $l = $this->l;
        $e = $this->e;

        $model = new ViewModel();
        $model->setTemplate('template');
        $e->setViewModel($model);

        $this->assertNull($l->createViewTemplate($e));
    }

    /**
     * @covers ::createViewTemplate
     */
    public function testCreateViewTemplateReturnsEarlyWithNoRouteMatch()
    {
        $l = $this->l;
        $e = $this->e;

        $model = new ViewModel();
        $e->setViewModel($model);

        $this->assertNull($l->createViewTemplate($e));
    }

    /**
     * @covers ::createViewTemplate
     */
    public function testCreateViewTemplateReturnsEarlyWithNoController()
    {
        $l = $this->l;
        $e = $this->e;

        $match = new RouteMatch(new Route('home', '/'));
        $e->setRouteMatch($match);

        $model = new ViewModel();
        $e->setViewModel($model);

        $this->assertNull($l->createViewTemplate($e));
    }

    /**
     * @covers ::createViewTemplate
     */
    public function testCreateViewTemplateReturnsEarlyWithNotFoundAction()
    {
        $l = $this->l;
        $e = $this->e;

        $match = new RouteMatch(new Route('home', '/'));
        $match->set('controller', 'foo');
        $match->set('action', 'not-found');
        $e->setRouteMatch($match);

        $model = new ViewModel();
        $e->setViewModel($model);

        $this->assertNull($l->createViewTemplate($e));
    }

    /**
     * @covers ::createViewTemplate, ::determinePackage
     */
    public function testCreateViewTemplateWithNoPackage()
    {
        $l = $this->l;
        $e = $this->e;

        $match = new RouteMatch(new Route('home', '/'));
        $match->set('controller', 'foo');
        $match->set('action', 'index');
        $e->setRouteMatch($match);

        $model = new ViewModel();
        $e->setViewModel($model);

        $l->createViewTemplate($e);
        $this->assertSame('foo/index', $model->getTemplate());
    }

    /**
     * @covers ::createViewTemplate, ::determinePackage
     */
    public function testCreateViewTemplate()
    {
        $l = $this->l;
        $e = $this->e;
        $i = $e->getApplication()->getInjector();
        $i['spiffy.mvc'] = ['controllers' => ['foo' => 'Application\Foo\FooController']];

        $match = new RouteMatch(new Route('home', '/'));
        $match->set('controller', 'foo');
        $match->set('action', 'index');
        $e->setRouteMatch($match);

        $model = new ViewModel();
        $e->setViewModel($model);

        $l->createViewTemplate($e);
        $this->assertSame('application/foo/index', $model->getTemplate());
    }

    protected function setUp()
    {
        $this->l = new InjectTemplateListener();

        $app = new Application();
        $i = $app->getInjector();
        $i['spiffy.mvc'] = [];

        $this->e = new MvcEvent($app);
    }
}
