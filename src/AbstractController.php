<?php

namespace Spiffy\Mvc;

use Spiffy\Dispatch\Dispatchable;
use Spiffy\View\ViewModel;

abstract class AbstractController implements Dispatchable
{
    /**
     * {@inheritDoc}
     */
    public function dispatch(array $params)
    {
        $action = isset($params['action']) ? $params['action'] : 'index';
        $action = $this->normalize($action);

        if (!method_exists($this, $action)) {
            $mvcEvent = isset($params['event']) ? $params['event'] : null;

            if (!$mvcEvent instanceof MvcEvent) {
                throw new Exception\MissingMvcEventException();
            }

            $mvcEvent->setError(MvcEvent::ERROR_NO_ACTION);
            $mvcEvent->setType(MvcEvent::EVENT_DISPATCH_ERROR);
            $result = $mvcEvent->getApplication()->events()->fire($mvcEvent);

            return $result->bottom();
        }

        return $this->$action();
    }

    /**
     * Default action for when an action is not found.
     *
     * @return array
     */
    public function notFound()
    {
        return new ViewModel();
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
