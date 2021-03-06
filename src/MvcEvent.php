<?php

namespace Spiffy\Mvc;

use Spiffy\Event\Event;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MvcEvent extends Event
{
    const ERROR_NO_CONTROLLER = 'mvc:error.no_controller';
    const ERROR_NO_ACTION = 'mvc:error.no_action';
    const ERROR_NO_ROUTE = 'mvc:error.no_route';
    const ERROR_EXCEPTION = 'mvc:error.exception';

    const EVENT_BOOTSTRAP = 'mvc:bootstrap';
    const EVENT_DISPATCH = 'mvc:dispatch';
    const EVENT_DISPATCH_ERROR = 'mvc:dispatch.error';
    const EVENT_FINISH = 'mvc:finish';
    const EVENT_RENDER = 'mvc:render';
    const EVENT_RENDER_ERROR = 'mvc:render.error';
    const EVENT_ROUTE = 'mvc:route';
    const EVENT_ROUTE_ERROR = 'mvc:route.error';

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var mixed
     */
    protected $dispatchResult;

    /**
     * @var string
     */
    protected $renderResult;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return null !== $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Spiffy\Mvc\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param \Spiffy\Route\RouteMatch $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * @return \Spiffy\Route\RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param \Spiffy\View\Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return \Spiffy\View\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $dispatchResult
     */
    public function setDispatchResult($dispatchResult)
    {
        $this->dispatchResult = $dispatchResult;
    }

    /**
     * @return mixed
     */
    public function getDispatchResult()
    {
        return $this->dispatchResult;
    }

    /**
     * @param string $renderResult
     */
    public function setRenderResult($renderResult)
    {
        $this->renderResult = $renderResult;
    }

    /**
     * @return string
     */
    public function getRenderResult()
    {
        return $this->renderResult;
    }
}
