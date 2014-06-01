<?php

namespace Spiffy\Mvc\Plugin;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\Plugin;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;
use Symfony\Component\HttpFoundation\Response;

class DispatchPlugin implements Plugin
{
    /**
     * @param Manager $events
     * @return void
     */
    public function plug(Manager $events)
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
            return $this->finish($e, $this->routeNotFound($e));
        }

        $i = $e->getApplication()->getInjector();

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('dispatcher');
        $controller = $match->get('controller');

        if (!$d->has($controller)) {
            return $this->finish($e, $this->controllerNotFound($e));
        }

        $params = $match->getParams();
        $params['event'] = $e;

        try {
            return $this->finish($e, $d->ispatch($controller, $params));
        } catch (\Exception $ex) {
        }

        return $this->finish($e, $this->controllerException($ex, $e));
    }

    /**
     * @param \Spiffy\Mvc\MvcEvent $e
     * @param mixed $result
     * @return null
     */
    protected function finish(MvcEvent $e, $result)
    {
        $e->setDispatchResult($result);

        if ($result instanceof Response) {
            $e->setResponse($result);
        }

        if ($result instanceof Model) {
            $e->setModel($result);
        }
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
            return null;
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
            return null;
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
            return null;
        }

        return $result->top();
    }
}
