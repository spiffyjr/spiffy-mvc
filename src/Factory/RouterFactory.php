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
        $routes = $i['mvc']['routes'];
        $r = new Router();

        foreach ($routes as $routeName => $params) {
            $defaults = ['controller' => $params[1]];

            if (isset($params[2])) {
                $defaults['action'] = $params[2];
            }

            $options = ['defaults' => $defaults];
            if (isset($params[3]) && is_array($params[3])) {
                $options = array_merge_recursive($params[3], $options);
            }

            $r->add($routeName, $params[0], $options);
        }

        return $r;
    }
}
