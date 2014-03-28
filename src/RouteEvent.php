<?php

namespace Spiffy\Mvc;

use Spiffy\Event\Event;

class RouteEvent extends Event
{
    const EVENT_ROUTE = 'route';

    /**
     * @var Application
     */
    protected $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        parent::__construct(static::EVENT_ROUTE);
    }
}
