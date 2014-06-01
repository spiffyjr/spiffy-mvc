<?php

namespace Spiffy\Mvc\Plugin;

use Spiffy\Event\EventManager;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\MvcEvent;

/**
 * @coversDefaultClass \Spiffy\Mvc\Plugin\CreateViewModelPlugin
 */
class CreateViewModelPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::attach
     */
    public function testAttach()
    {
        $events = new EventManager();
        $l = new CreateViewModelPlugin();
        $l->attach($events);

        $this->assertCount(1, $events->getEvents());
        $this->assertCount(2, $events->getEvents(MvcEvent::EVENT_DISPATCH));
    }

    /**
     * @covers ::createModelFromArray
     */
    public function testModelFromArray()
    {
        $l = new CreateViewModelPlugin();
        $event = new MvcEvent(new Application());

        $l->createModelFromArray($event);
        $this->assertNull($event->getModel());

        $event->setDispatchResult(['foo' => 'bar']);
        $l->createModelFromArray($event);

        $model = $event->getModel();
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
        $this->assertSame(['foo' => 'bar'], $model->getVariables());
    }

    /**
     * @covers ::createModelFromNull
     */
    public function testModelFromNull()
    {
        $l = new CreateViewModelPlugin();
        $event = new MvcEvent(new Application());
        $event->setDispatchResult(['foo' => 'bar']);

        $l->createModelFromNull($event);
        $this->assertNull($event->getModel());

        $event->setDispatchResult(null);
        $l->createModelFromNull($event);
        $model = $event->getModel();
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
    }
}
