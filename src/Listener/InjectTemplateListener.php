<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;

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
        $model = $e->getModel();
        if (!$model instanceof Model || $model->getTemplate()) {
            return;
        }

        $match = $e->getRouteMatch();
        if (!$match instanceof RouteMatch) {
            return;
        }

        $controller = $match->get('controller');
        if (!$controller) {
            return;
        }

        // todo: should we null this out with a not found action?
        $action = $match->get('action', 'index');
        if ($action == 'not-found') {
            return;
        }

        $i = $e->getApplication()->getInjector();
        $map = isset($i['spiffy.mvc']['controllers']) ? $i['spiffy.mvc']['controllers'] : [];
        $package = $this->determinePackage($controller, $map);

        $model->setTemplate(ltrim(implode('/', [$package, $controller, $action]), '/'));
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
