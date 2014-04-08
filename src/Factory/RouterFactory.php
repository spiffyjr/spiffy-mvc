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
        $routes = $i['spiffy.mvc']['routes'];
        $r = new Router();

        foreach ($routes as $routeName => $params) {
            $defaults = ['controller' => $params[1]];

            if (isset($params[2])) {
                $defaults['action'] = $params[2];
            }

            if (isset($params[3]) && is_array($params[3])) {
                $defaults = array_merge($params[3], $defaults);
            }

            $r->add($routeName, $params[0], ['defaults' => $defaults]);
        }

        return $r;
    }
}
