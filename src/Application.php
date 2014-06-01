<?php

namespace Spiffy\Mvc;

use Spiffy\Event\EventsAwareTrait;
use Spiffy\Inject\Injector;
use Spiffy\Package\Feature\ConfigProvider;
use Spiffy\Package\Feature\Exception\MissingOptionException;
use Spiffy\Package\Feature\OptionsProvider;
use Spiffy\Package\Feature\OptionsProviderTrait;

class Application implements ConfigProvider
{
    use EventsAwareTrait;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @return MvcEvent
     */
    public function getEvent()
    {
        if (!$this->event instanceof MvcEvent) {
            $i = $this->getInjector();

            $this->event = new MvcEvent($this);
            $this->event->setRequest($i->nvoke('request'));
        }
        return $this->event;
    }

    /**
     * @param \Spiffy\Inject\Injector $injector
     */
    public function setInjector($injector)
    {
        $this->injector = $injector;
    }

    /**
     * @return Injector
     */
    public function getInjector()
    {
        if (!$this->injector instanceof Injector) {
            $this->injector = new Injector();
        }
        return $this->injector;
    }

    /**
     * Bootstrap the application.
     */
    public function bootstrap()
    {
        $event = $this->getEvent();
        $event->setType(MvcEvent::EVENT_BOOTSTRAP);
        $this->events()->fire($event);
    }

    /**
     * Runs the application by firing the bootstrap, route,
     * dispatch, render, and response plugins.
     */
    public function run()
    {
        $this->bootstrap();

        $event = $this->getEvent();

        $event->setType(MvcEvent::EVENT_ROUTE);
        $this->events()->fire($event);

        $event->setType(MvcEvent::EVENT_DISPATCH);
        $this->events()->fire($event);

        $event->setType(MvcEvent::EVENT_RENDER);
        $this->events()->fire($event);

        $event->setType(MvcEvent::EVENT_FINISH);
        $this->events()->fire($event);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/config.php';
    }
}
