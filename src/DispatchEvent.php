<?php

namespace Spiffy\Mvc;

use Spiffy\Event\Event;

class DispatchEvent extends Event
{
    const EVENT_DISPATCH = 'dispatch';

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
        parent::__construct(static::EVENT_DISPATCH);
    }
}
