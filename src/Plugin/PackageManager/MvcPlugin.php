<?php

namespace Spiffy\Mvc\Plugin\PackageManager;

use Spiffy\Event\Event;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Inject\Injector;
use Spiffy\Mvc\MvcPackage;
use Spiffy\Package\PackageManager;

class MvcPlugin implements Plugin
{
    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @param Injector $injector
     */
    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * @param Manager $events
     * @return void
     */
    public function plug(Manager $events)
    {
        $events->on(PackageManager::EVENT_LOAD_POST, [$this, 'injectParameters'], -1000);
        $events->on(PackageManager::EVENT_LOAD_POST, [$this, 'injectServices'], -1000);
        $events->on(PackageManager::EVENT_MERGE_CONFIG, [$this, 'injectRouteConfig'], 100);
    }

    /**
     * @param Event $e
     */
    public function injectParameters(Event $e)
    {
        /** @var \Spiffy\Package\PackageManager $manager */
        $manager = $e->getTarget();
        $i = $this->injector;

        foreach ($manager->getPackages() as $package) {
            if (!$package instanceof MvcPackage) {
                continue;
            }

            $config = $manager->getMergedConfig();
            if (!isset($config[$package->getName()])) {
                continue;
            }

            $i[$package->getName()] = $config[$package->getName()];
        }
    }

    /**
     * @param Event $e
     * @return array
     */
    public function injectRouteConfig(Event $e)
    {
        /** @var \Spiffy\Package\PackageManager $manager */
        $manager = $e->getTarget();
        $config = [];

        foreach ($manager->getPackages() as $package) {
            if (!$package instanceof MvcPackage) {
                continue;
            }

            $routes = $package->getRoutes();
            if (0 == count($routes)) {
                continue;
            }

            $config = $manager->merge($config, ['mvc' => ['routes' => $package->getRoutes()]]);
        }

        return $config;
    }

    /**
     * @param Event $e
     */
    public function injectServices(Event $e)
    {
        /** @var \Spiffy\Package\PackageManager $manager */
        $manager = $e->getTarget();
        $i = $this->injector;

        foreach ($manager->getPackages() as $package) {
            if (!$package instanceof MvcPackage) {
                continue;
            }

            foreach ($package->getServices() as $name => $spec) {
                $i->nject($name, $spec);
            }
        }
    }
}
