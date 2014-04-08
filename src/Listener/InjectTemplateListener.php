<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\RouteMatch;

class InjectTemplateListener implements Listener
{
    /**
     * @param Manager $events
     * @return void
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_DISPATCH, [$this, 'createViewTemplate'], -110);
    }

    /**
     * @param MvcEvent $e
     */
    public function createViewTemplate(MvcEvent $e)
    {
        $model = $e->getViewModel();
        if ($model->getTemplate()) {
            return;
        }

        $i = $e->getApplication()->getInjector();
        $match = $e->getRouteMatch();

        if (!$match instanceof RouteMatch) {
            return;
        }

        $controller = $match->get('controller');

        if (!$controller) {
            return;
        }

        $action = $match->get('action', 'index');

        if ($action == 'not-found') {
            return;
        }

        $package = $this->determinePackage($controller, $i['spiffy.mvc']['controllers']);
        $model->setTemplate(implode('/', [$package, $controller, $action]));
    }

    /**
     * @param string $controller
     * @param array $map
     * @return string
     */
    protected function determinePackage($controller, array $map)
    {
        if (!isset($map[$controller])) {
            return '';
        }

        $controller = $map[$controller];
        $parts = explode('\\', $controller);
        return strtolower(array_shift($parts));
    }
}
