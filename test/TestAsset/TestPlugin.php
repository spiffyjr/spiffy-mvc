<?php

namespace Spiffy\Mvc\TestAsset;

use Spiffy\Event\Plugin;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;

class TestPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(MvcEvent::EVENT_DISPATCH, function() { return 'test'; }, 10000);
    }
}
