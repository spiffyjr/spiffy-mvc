<?php

namespace Spiffy\Mvc;

use Spiffy\Package\Feature\ConfigProvider;
use Spiffy\Package\Feature\OptionsProvider;
use Spiffy\Package\Feature\PathProvider;

interface MvcPackage extends ConfigProvider, OptionsProvider, PathProvider
{
    /**
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return array
     */
    public function getRoutes();

    /**
     * @return array
     */
    public function getServices();

    /**
     * @param ConsoleApplication $console
     * @return void
     */
    public function initializeConsole(ConsoleApplication $console);
}
