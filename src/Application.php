<?php

namespace Spiffy\Mvc;

use Spiffy\Event\EventsAwareTrait;
use Spiffy\Event\Manager;
use Spiffy\Inject\Injector;
use Spiffy\Mvc\Listener\RouteListener;

class Application
{
    use EventsAwareTrait;

    /**
     * @var DispatchEvent
     */
    protected $dispatchEvent;

    /**
     * @var RouteEvent
     */
    protected $routeEvent;

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @param Injector $injector
     */
    public function __construct(Injector $injector)
    {
        $this->dispatchEvent = new DispatchEvent($this);
        $this->routeEvent = new RouteEvent($this);
        $this->injector = $injector;
    }

    /**
     * @return Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }

    public function run()
    {
        $this->events()->fire($this->routeEvent);
        $this->events()->fire($this->dispatchEvent);
    }

    /**
     * @param Manager $events
     */
    protected function initEvents(Manager $events)
    {
        $events->attach(new RouteListener());
    }
}
