<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Inject\ServiceFactory;
use Spiffy\Route\Router;

class RouterFactory implements ServiceFactory
{
    /**
     * @param Injector $i
     * @return Router
     */
    public function createService(Injector $i)
    {
        return new Router();
    }
}
