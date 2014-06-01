<?php

namespace Spiffy\Mvc\Plugin;

use Spiffy\Event\Plugin;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\View\ViewModel;

class CreateViewModelPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(MvcEvent::EVENT_DISPATCH, [$this, 'createModelFromArray'], -90);
        $events->on(MvcEvent::EVENT_DISPATCH, [$this, 'createModelFromNull'], -90);
    }

    /**
     * @param MvcEvent $e
     */
    public function createModelFromArray(MvcEvent $e)
    {
        $result = $e->getDispatchResult();
        if (!is_array($result) || $e->getError()) {
            return;
        }
        $e->setModel(new ViewModel($result));
    }

    /**
     * @param MvcEvent $e
     */
    public function createModelFromNull(MvcEvent $e)
    {
        $result = $e->getDispatchResult();
        if (null !== $result || $e->getError()) {
            return;
        }
        $e->setModel(new ViewModel());
    }
}
