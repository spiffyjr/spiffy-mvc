<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\RouteMatch;

class DispatchListener implements Listener
{
    /**
     * @param Manager $events
     * @return void
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * @param MvcEvent $e
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $match = $e->getRouteMatch();
        if (!$match instanceof RouteMatch) {
            return $this->finish($this->routeNotFound($e, $e), $e);
        }

        $i = $e->getApplication()->getInjector();

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('dispatcher');
        $controller = $match->get('controller');

        if (!$d->has($controller)) {
            return $this->finish($this->controllerNotFound($e), $e);
        }

        $params = $match->getParams();
        $params['event'] = $e;

        try {
            $result = $d->ispatch($controller, $params);
        } catch (\Exception $ex) {
            $result = $this->controllerException($ex, $e);
        }

        return $this->finish($result, $e);
    }

    /**
     * @param mixed $result
     * @param MvcEvent $e
     * @return mixed
     */
    protected function finish($result, MvcEvent $e)
    {
        $e->setResult($result);
        return $result;
    }

    /**
     * @param MvcEvent $e
     * @return mixed
     */
    protected function routeNotFound(MvcEvent $e)
    {
        $e->setError(MvcEvent::ERROR_NO_ROUTE);
        $e->setType(MvcEvent::EVENT_ROUTE_ERROR);
        $result = $e->getApplication()->events()->fire($e);

        if ($result->count() == 0) {
            return $e->getResult();
        }

        return $result->top();
    }

    /**
     * @param \Exception $ex
     * @param MvcEvent $e
     * @return mixed
     */
    protected function controllerException(\Exception $ex, MvcEvent $e)
    {
        $e->setError(MvcEvent::ERROR_EXCEPTION);
        $e->setType(MvcEvent::EVENT_DISPATCH_ERROR);
        $e->set('exception', $ex);
        $result = $e->getApplication()->events()->fire($e);

        if ($result->count() == 0) {
            return $e->getResult();
        }

        return $result->top();
    }

    /**
     * @param MvcEvent $e
     * @return mixed
     */
    protected function controllerNotFound(MvcEvent $e)
    {
        $e->setError(MvcEvent::ERROR_NO_CONTROLLER);
        $e->setType(MvcEvent::EVENT_DISPATCH_ERROR);
        $result = $e->getApplication()->events()->fire($e);

        if ($result->count() == 0) {
            return $e->getResult();
        }

        return $result->top();
    }
}
