<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\Router;
use Symfony\Component\HttpFoundation\Request;

class RouteListener implements Listener
{
    /**
     * @param Manager $events
     * @return void
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -100);
    }

    /**
     * @param MvcEvent $e
     * @throws Exception\MissingRouterException
     * @throws Exception\MissingRequestException
     */
    public function onRoute(MvcEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        $request = $i->nvoke('request');
        if (!$request instanceof Request) {
            throw new Exception\MissingRequestException();
        }

        $router = $i->nvoke('router');
        if (!$router instanceof Router) {
            throw new Exception\MissingRouterException();
        }

        $match = $router->match($request->getRequestUri(), $request->server->all());

        if (null === $match) {
            $e->setType(MvcEvent::EVENT_ROUTE_ERROR);
            $app->events()->fire($e);
        }

        $e->setRouteMatch($match);
    }
}
