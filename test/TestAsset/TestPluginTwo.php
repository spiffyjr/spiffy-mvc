<?php

namespace Spiffy\Mvc\TestAsset;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;

class TestPluginTwo implements Listener
{
    /**
     * {@inheritDoc}
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_DISPATCH, function() { return 'testtwo'; }, 10000);
    }
}
