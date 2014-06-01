<?php

namespace Spiffy\Mvc\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Mvc\MvcEvent;
use Spiffy\Mvc\MvcPackage;

class BootstrapPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(MvcEvent::EVENT_BOOTSTRAP, [$this, 'bootstrapPackages']);
    }

    /**
     * @param MvcEvent $e
     */
    public function bootstrapPackages(MvcEvent $e)
    {
        $i = $e->getApplication()->getInjector();
        $pm = $i->nvoke('package-manager');

        foreach ($pm->getPackages() as $package) {
            if ($package instanceof MvcPackage) {
                $package->bootstrap($e->getApplication());
            }
        }
    }
}
