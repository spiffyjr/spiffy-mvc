<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Inject\Injector;
use Spiffy\Inject\InjectorUtils;
use Spiffy\Inject\ServiceFactory;

class DispatcherFactory implements ServiceFactory
{
    /**
     * @param Injector $i
     * @return Dispatcher
     */
    public function createService(Injector $i)
    {
        $controllers = $i['mvc']['controllers'];

        $d = new Dispatcher();

        foreach ($controllers as $controllerName => $controller) {
            $d->add($controllerName, InjectorUtils::get($i, $controller));
        }

        return $d;
    }
}
