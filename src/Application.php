<?php

namespace Spiffy\Mvc;

use Spiffy\Event\EventsAwareTrait;
use Spiffy\Inject\Injector;

class Application
{
    use EventsAwareTrait;

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @param Injector $injector
     */
    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * @return Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }
}
