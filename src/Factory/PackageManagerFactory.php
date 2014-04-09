<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Package\PackageManager;

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

        if (isset($config['override_pattern'])) {
            $pm->setOverridePattern($config['override_pattern']);
        }

        if (isset($config['override_flags'])) {
            $pm->setOverrideFlags($config['override_flags']);
        }

        foreach ($packages as $packageName => $fqcn) {
            if (is_numeric($packageName)) {
                $packageName = $fqcn;
                $fqcn = null;
            }
            $pm->add($packageName, $fqcn);
        }

        $pm->load();

        return $pm;
    }
}
