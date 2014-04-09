<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\View\ViewModel;

class CreateViewModelListener implements Listener
{
    /**
     * {@inheritDoc}
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_DISPATCH, [$this, 'createModelFromArray'], -90);
        $events->on(MvcEvent::EVENT_DISPATCH, [$this, 'createModelFromNull'], -90);
    }

    /**
     * @param MvcEvent $e
     */
    public function createModelFromArray(MvcEvent $e)
    {
        $result = $e->getResult();
        if (!is_array($result)) {
            return;
        }
        $e->setViewModel(new ViewModel($result));
    }

    /**
     * @param MvcEvent $e
     */
    public function createModelFromNull(MvcEvent $e)
    {
        $result = $e->getResult();
        if (null !== $result) {
            return;
        }
        $e->setViewModel(new ViewModel());
    }
}
