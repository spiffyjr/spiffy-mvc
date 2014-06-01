<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Package\PackageManager;
use Spiffy\Package\Plugin as PackagePlugin;

class PackageManagerFactory
{
    /**
     * @param array $config
     * @return PackageManager
     */
    public function createService(array $config)
    {
        $packages = $config['packages'];

        $pm = new PackageManager();

        foreach ($packages as $packageName => $fqcn) {
            if (is_numeric($packageName)) {
                $packageName = $fqcn;
                $fqcn = null;
            }
            $pm->add($packageName, $fqcn);
        }

        $events = $pm->events();
        $events->plug(new PackagePlugin\ConfigMergePlugin($config['override_pattern'], $config['override_flags']));
        $events->plug(new PackagePlugin\LoadModulesPlugin());
        $events->plug(new PackagePlugin\OptionsProviderPlugin());

        return $pm;
    }
}
