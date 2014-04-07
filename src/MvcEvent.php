<?php

namespace Spiffy\Mvc;

use Spiffy\Event\Event;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MvcEvent extends Event
{
    const ERROR_NO_CONTROLLER = 'spiffy-mvc.error.no_controller';
    const ERROR_NO_ACTION = 'spiffy-mvc.error.no_action';
    const ERROR_NO_ROUTE = 'spiffy-mvc.error.no_route';
    const ERROR_EXCEPTION = 'spiffy-mvc.error.exception';

    const EVENT_BOOTSTRAP = 'spiffy-mvc.bootstrap';
    const EVENT_DISPATCH = 'spiffy-mvc.dispatch';
    const EVENT_DISPATCH_ERROR = 'spiffy-mvc.dispatch.error';
    const EVENT_FINISH = 'spiffy-mvc.finish';
    const EVENT_RENDER = 'spiffy-mvc.render';
    const EVENT_RENDER_ERROR = 'spiffy-mvc.render.error';
    const EVENT_ROUTE = 'spiffy-mvc.route';
    const EVENT_ROUTE_ERROR = 'spiffy-mvc.route.error';

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var Model
     */
    protected $viewModel;

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
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
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
    public function setRouteMatch($routeMatch)
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
     * @param \Spiffy\View\Model $viewModel
     */
    public function setViewModel($viewModel)
    {
        $this->viewModel = $viewModel;
    }

    /**
     * @return \Spiffy\View\Model
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }
}
