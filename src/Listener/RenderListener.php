<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\View\ViewModel;

class RenderListener implements Listener
{
    /**
     * @param Manager $events
     * @return void
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_RENDER, [$this, 'onRender']);
    }

    /**
     * @param MvcEvent $e
     */
    public function onRender(MvcEvent $e)
    {
        $model = $e->getViewModel();

        if (!$model instanceof ViewModel) {
            return;
        }

        try {
            $result = $this->renderer->render($e->getViewModel());
        } catch (\Exception $ex) {
            $result = $this->renderException($ex, $e);
        }

        $e->setResult($result);
    }

    /**
     * @param \Exception $ex
     * @param MvcEvent $e
     * @return mixed
     */
    protected function renderException(\Exception $ex, MvcEvent $e)
    {
        $e->setError(MvcEvent::ERROR_EXCEPTION);
        $e->setType(MvcEvent::EVENT_RENDER_ERROR);
        $e->set('exception', $ex);
        $e->getApplication()->events()->fire($e);

        return $this->renderer->render($e->getViewModel());
    }
}
