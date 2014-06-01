<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Mvc\ConsoleApplication;
use Spiffy\Mvc\MvcPackage;

class DefaultConsoleFactory
{
    public function create(array $config)
    {
        $appFactory = new DefaultApplicationFactory();
        $console = new ConsoleApplication($appFactory->create($config));

        $this->injectCommands($console);

        return $console;
    }

    /**
     * @param ConsoleApplication $console
     */
    protected function injectCommands(ConsoleApplication $console)
    {
        $app = $console->getApplication();

        /** @var \Spiffy\Package\PackageManager $pm */
        $pm = $app->getInjector()->get('package-manager');
        foreach ($pm->getPackages() as $package) {
            if ($package instanceof MvcPackage) {
                $package->initializeConsole($console);
            }
        }
    }
}
