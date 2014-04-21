<?php

namespace Spiffy\Mvc;

use Spiffy\Dispatch\Dispatchable;
use Spiffy\View\ViewModel;

abstract class AbstractController implements Dispatchable
{
    /**
     * @var MvcEvent
     */
    protected $mvcEvent;

    /**
     * {@inheritDoc}
     */
    public function dispatch(array $params)
    {
        $mvcEvent = isset($params['event']) ? $params['event'] : null;
        if (!$mvcEvent instanceof MvcEvent) {
            throw new Exception\MissingMvcEventException(
                'Failed to dispatch: no MvcEvent available in params.'
            );
        }

        $action = isset($params['action']) ? $params['action'] : 'index';
        $action = $this->normalize($action);

        if (!method_exists($this, $action)) {
            $mvcEvent->setError(MvcEvent::ERROR_NO_ACTION);
            $mvcEvent->setType(MvcEvent::EVENT_DISPATCH_ERROR);
            $result = $mvcEvent->getApplication()->events()->fire($mvcEvent);

            return $result->bottom();
        }

        $this->mvcEvent = $mvcEvent;
        return $this->$action();
    }

    /**
     * Default action for when an action is not found.
     *
     * @return ViewModel
     */
    public function notFound()
    {
        return new ViewModel();
    }

    /**
     * @return \Spiffy\Mvc\MvcEvent
     */
    public function getMvcEvent()
    {
        return $this->mvcEvent;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        $this->assertDispatched();
        return $this->getMvcEvent()->getRequest();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        $this->assertDispatched();
        return $this->getMvcEvent()->getResponse();
    }

    /**
     * @return \Spiffy\Inject\Injector
     */
    public function getInjector()
    {
        $this->assertDispatched();
        return $this->mvcEvent->getApplication()->getInjector();
    }

    /**
     * @throws Exception\MissingMvcEventException
     */
    protected function assertDispatched()
    {
        if (!$this->mvcEvent instanceof MvcEvent) {
            throw new Exception\MissingMvcEventException(
                'MvcEvent is not available: has the controller been dispatched?.'
            );
        }
    }

    /**
     * @param string $action
     * @return string
     */
    protected function normalize($action)
    {
        $callback = function ($matches) {
            return ucfirst($matches[1]);
        };

        return preg_replace_callback('@-([a-zA-Z])@', $callback, $action);
    }
}
