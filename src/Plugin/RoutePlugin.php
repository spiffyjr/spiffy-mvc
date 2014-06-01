<?php

namespace Spiffy\Mvc\Plugin;

use Spiffy\Event\Plugin;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;

class RoutePlugin implements Plugin
{
    /**
     * @param Manager $events
     * @return void
     */
    public function plug(Manager $events)
    {
        $events->on(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -100);
    }

    /**
     * @param MvcEvent $e
     */
    public function onRoute(MvcEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $i->nvoke('request');

        /** @var \Spiffy\Route\Router $router */
        $router = $i->nvoke('router');

        $match = $router->match($request->getRequestUri(), $request->server->all());
        if (null === $match) {
            $e->setType(MvcEvent::EVENT_ROUTE_ERROR);
            $app->events()->fire($e);
            return;
        }

        $e->setRouteMatch($match);
    }
}
