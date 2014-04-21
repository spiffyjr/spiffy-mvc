<?php

namespace Spiffy\Mvc;

use Spiffy\Event\EventManager;
use Spiffy\Mvc\TestAsset\TestStrategy;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Mvc\ViewManager
 */
class ViewManagerTest extends \PHPUnit_Framework_TestCase
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
     * @var array
     */
    protected $options;

    /**
     * @var ViewManager
     */
    protected $vm;

    /**
     * @covers ::attach
     */
    public function testAttach()
    {
        $events = new EventManager();
        $vm = $this->vm;
        $vm->attach($events);

        $this->assertCount(2, $events->getEvents());
        $this->assertCount(1, $events->getEvents(MvcEvent::EVENT_BOOTSTRAP));
        $this->assertCount(1, $events->getEvents(MvcEvent::EVENT_RENDER));
    }

    /**
     * @covers ::onRender
     */
    public function testRender()
    {
        $this->app->setOptions([
            'view_manager' => [
                'strategies' => [
                    'Spiffy\Mvc\TestAsset\TestStrategy',
                ]
            ]
        ]);

        $e = $this->event;
        $e->setModel(new ViewModel());

        $vm = $this->vm;
        $vm->onBootstrap($e);
        $vm->onRender($e);

        $this->assertSame('rendered', $e->getRenderResult());
    }

    /**
     * @covers ::onRender
     */
    public function testCanRenderReturnsIfResponseExists()
    {
        $e = $this->event;
        $e->setResponse(new Response());

        $this->assertNull($this->vm->onRender($e));
    }

    /**
     * @covers ::onRender
     */
    public function testCanRenderReturnsIfNoViewModel()
    {
        $e = $this->event;
        $this->assertNull($this->vm->onRender($e));
    }

    /**
     * @covers ::onRender
     */
    public function testRenderHandlesExceptions()
    {
        $this->app->setOptions([
            'view_manager' => [
                'strategies' => [
                    'Spiffy\Mvc\TestAsset\ExceptionStrategy',
                ]
            ]
        ]);

        $e = $this->event;
        $e->setModel(new ViewModel());

        $vm = $this->vm;
        $vm->onBootstrap($this->event);
        $vm->onRender($e);

        $this->assertTrue($e->hasError());
        $this->assertSame(MvcEvent::ERROR_EXCEPTION, $e->getError());
        $this->assertSame(MvcEvent::EVENT_RENDER_ERROR, $e->getType());
        $this->assertInstanceOf('RuntimeException', $e->get('exception'));
    }

    /**
     * @covers ::onRender
     */
    public function testRenderSkipsCanRenderFalse()
    {
        $this->app->setOptions([
            'view_manager' => [
                'strategies' => [
                    'Spiffy\Mvc\TestAsset\NoRenderStrategy',
                ]
            ]
        ]);

        $e = $this->event;
        $e->setModel(new ViewModel());

        $vm = $this->vm;
        $vm->onBootstrap($this->event);
        $vm->onRender($this->event);

        $this->assertSame('rendered', $this->event->getRenderResult());
    }

    /**
     * @covers ::onBootstrap
     */
    public function testOnBootstrap()
    {
        $options = $this->options['view_manager'];
        $vm = $this->vm;
        $vm->onBootstrap($this->event);

        $this->assertSame($options['not_found_template'], $vm->getNotFoundTemplate());
        $this->assertSame($options['error_template'], $vm->getErrorTemplate());
    }

    /**
     * @covers ::registerStrategies
     */
    public function testRegisterStrategiesUnsetsNonStringStrategies()
    {
        $this->app->setOptions([
            'view_manager' => [
                'strategies' => [
                    false,
                    null
                ]
            ]
        ]);

        $vm = $this->vm;
        $vm->onBootstrap($this->event);

        $refl = new \ReflectionClass($vm);
        $strategies = $refl->getProperty('strategies');
        $strategies->setAccessible(true);

        $this->assertCount(0, $strategies->getValue($vm));
    }

    /**
     * @covers ::registerStrategies
     */
    public function testRegisterStrategiesUnsetsInvalidStrategies()
    {
        $this->app->setOptions([
            'view_manager' => [
                'strategies' => [
                    'StdClass'
                ]
            ]
        ]);

        $vm = $this->vm;
        $vm->onBootstrap($this->event);

        $refl = new \ReflectionClass($vm);
        $strategies = $refl->getProperty('strategies');
        $strategies->setAccessible(true);

        $this->assertCount(0, $strategies->getValue($vm));
    }

    /**
     * @covers ::registerStrategies
     */
    public function testRegisterStrategiesReadsFromInjector()
    {
        $this->app->setOptions([
            'view_manager' => [
                'strategies' => [
                    'test'
                ]
            ]
        ]);

        $i = $this->app->getInjector();
        $i->nject('test', new TestStrategy());

        $vm = $this->vm;
        $vm->onBootstrap($this->event);

        $refl = new \ReflectionClass($vm);
        $strategies = $refl->getProperty('strategies');
        $strategies->setAccessible(true);
        $strategies = $strategies->getValue($vm);

        $this->assertCount(1, $strategies);
        $this->assertInstanceOf('Spiffy\Mvc\TestAsset\TestStrategy', $strategies[0]);
    }

    /**
     * @covers ::registerStrategies
     */
    public function testRegisterStrategiesReadsFromString()
    {
        $this->app->setOptions([
            'view_manager' => [
                'strategies' => [
                    'Spiffy\Mvc\TestAsset\TestStrategy'
                ]
            ]
        ]);

        $vm = $this->vm;
        $vm->onBootstrap($this->event);

        $refl = new \ReflectionClass($vm);
        $strategies = $refl->getProperty('strategies');
        $strategies->setAccessible(true);
        $strategies = $strategies->getValue($vm);

        $this->assertCount(1, $strategies);
        $this->assertInstanceOf('Spiffy\Mvc\TestAsset\TestStrategy', $strategies[0]);
    }

    /**
     * @covers ::setNotFoundTemplate, ::getNotFoundTemplate
     */
    public function testSetGetNotFoundTemplate()
    {
        $vm = $this->vm;
        $this->assertSame('error/404', $vm->getNotFoundTemplate());
        $vm->setNotFoundTemplate('not-found');
        $this->assertSame('not-found', $vm->getNotFoundTemplate());
    }

    /**
     * @covers ::setErrorTemplate, ::getErrorTemplate
     */
    public function testSetGetErrorTemplate()
    {
        $vm = $this->vm;
        $this->assertSame('error/error', $vm->getErrorTemplate());
        $vm->setErrorTemplate('error');
        $this->assertSame('error', $vm->getErrorTemplate());
    }

    protected function setUp()
    {
        $this->options = $options = [
            'view_manager' => [
                'not_found_template' => 'not-found',
                'error_template' => 'error',
                'strategies' => []
            ]
        ];
        $this->app = $app = new Application();
        $app->setOptions($options);
        $this->event = new MvcEvent($app);

        $this->vm = new ViewManager(new TestStrategy());
    }
}
