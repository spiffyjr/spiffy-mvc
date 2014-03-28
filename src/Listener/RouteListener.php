<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\RouteEvent;

class RouteListener implements Listener
{
    /**
     * @param Manager $events
     * @return void
     */
    public function attach(Manager $events)
    {
        $events->on(RouteEvent::EVENT_ROUTE, [$this, 'onRoute']);
    }

    /**
     * @param RouteEvent $e
     */
    public function onRoute(RouteEvent $e)
    {

    }
}
