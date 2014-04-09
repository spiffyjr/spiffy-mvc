<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\EventManager;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\MvcEvent;

/**
 * @coversDefaultClass \Spiffy\Mvc\Listener\CreateViewModelListener
 */
class CreateViewModelListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::attach
     */
    public function testAttach()
    {
        $events = new EventManager();
        $l = new CreateViewModelListener();
        $l->attach($events);

        $this->assertCount(1, $events->getEvents());
        $this->assertCount(2, $events->getEvents(MvcEvent::EVENT_DISPATCH));
    }

    /**
     * @covers ::createModelFromArray
     */
    public function testModelFromArray()
    {
        $l = new CreateViewModelListener();
        $event = new MvcEvent(new Application());

        $l->createModelFromArray($event);
        $this->assertNull($event->getViewModel());

        $event->setResult(['foo' => 'bar']);
        $l->createModelFromArray($event);

        $model = $event->getViewModel();
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
        $this->assertSame(['foo' => 'bar'], $model->getVariables());
    }

    /**
     * @covers ::createModelFromNull
     */
    public function testModelFromNull()
    {
        $l = new CreateViewModelListener();
        $event = new MvcEvent(new Application());
        $event->setResult(['foo' => 'bar']);

        $l->createModelFromNull($event);
        $this->assertNull($event->getViewModel());

        $event->setResult(null);
        $l->createModelFromNull($event);
        $model = $event->getViewModel();
        $this->assertInstanceOf('Spiffy\View\ViewModel', $model);
    }
}
