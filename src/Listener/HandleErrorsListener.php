<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Response;

class HandleErrorsListener implements Listener
{
    /**
     * @var array
     */
    protected $allowedErrors = [
        MvcEvent::ERROR_NO_CONTROLLER,
        MvcEvent::ERROR_NO_ACTION,
        MvcEvent::ERROR_NO_ROUTE
    ];

    /**
     * {@inheritDoc}
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'handleErrors']);
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'handleExceptions']);
        $events->on(MvcEvent::EVENT_ROUTE_ERROR, [$this, 'handleErrors']);
        $events->on(MvcEvent::EVENT_RENDER_ERROR, [$this, 'handleExceptions']);
    }

    /**
     * @param MvcEvent $e
     */
    public function handleExceptions(MvcEvent $e)
    {
        $error = $e->getError();
        if ($error !== MvcEvent::ERROR_EXCEPTION) {
            return;
        }

        $i = $e->getApplication()->getInjector();
        $vm = $i->nvoke('view_manager');

        $model = new ViewModel(['exception' => $e->get('exception')]);
        $model->setTemplate($vm->getErrorTemplate());

        $response = $e->getResponse();
        if (!$response instanceof Response) {
            $response = new Response();
        }

        $response->setStatusCode(500);
        $e->setResponse($response);
        $e->setViewModel($model);
        $e->setResult($model);
    }

    /**
     * @param MvcEvent $e
     * @return \Spiffy\View\ViewModel
     */
    public function handleErrors(MvcEvent $e)
    {
        $error = $e->getError();
        if (!in_array($error, $this->allowedErrors)) {
            return null;
        }

        $i = $e->getApplication()->getInjector();
        $vm = $i->nvoke('view_manager');

        $params = ['error' => $error];

        switch ($e->getError()) {
            case MvcEvent::ERROR_NO_CONTROLLER:
                $match = $e->getRouteMatch();
                $params['controller'] = $match->get('controller');
                break;
            case MvcEvent::ERROR_NO_ACTION:
                $match = $e->getRouteMatch();
                $params['controller'] = $match->get('controller');
                $params['action'] = $match->get('action', 'index');
                break;
        }

        $model = new ViewModel($params);
        $model->setTemplate($vm->getNotFoundTemplate());

        $response = $e->getResponse();
        if (!$response instanceof Response) {
            $response = new Response();
        }

        $response->setStatusCode(404);
        $e->setResponse($response);
        $e->setViewModel($model);
        $e->setResult($model);

        return $model;
    }
}
