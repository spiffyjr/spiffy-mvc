<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Inject\ServiceFactory;
use Spiffy\Mvc\ViewManager;

class ViewManagerFactory implements ServiceFactory
{
    /**
     * @param Injector $i
     * @return ViewManager
     */
    public function createService(Injector $i)
    {
        $options = $i['spiffy.mvc']['view_manager'];
        $strategy = $options['default_strategy'];
        $strategy = $i->has($strategy) ? $i->nvoke($strategy) : new $strategy();

        return new ViewManager($strategy);
    }
}
