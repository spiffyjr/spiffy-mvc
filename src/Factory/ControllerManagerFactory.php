<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Inject\ServiceFactory;

class ControllerManagerFactory implements ServiceFactory
{
    /**
     * @param Injector $i
     * @return Injector
     */
    public function createService(Injector $i)
    {
        $pm = $i->nvoke('package_manager');
        exit;
    }
}
