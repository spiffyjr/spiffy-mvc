<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Mvc\Application;
use Spiffy\Mvc\Listener;
use Spiffy\Mvc\View\ViewManager;
use Spiffy\Package\Feature\OptionsProvider;
use Spiffy\Package\PackageManager;
use Symfony\Component\HttpFoundation\Request;

class DefaultApplicationFactory
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return Application
     */
    public function createService()
    {
        $config = $this->config;
        $env = isset($config['environment']) ? (array) $config['environment'] : [];
        $packages = isset($config['packages']) ? (array) $config['packages'] : [];

        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }

        $pm = new PackageManager();
        $pm->add('spiffy.mvc', 'Spiffy\\Mvc\\Application');

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

        /** @var \Spiffy\Mvc\Application $application */
        $application = $pm->getPackage('spiffy.mvc');
        $i = $application->getInjector();

        // inject all packages (and options if they exist)
        foreach ((array) $pm->getPackages() as $packageName => $package) {
            $i->nject($packageName, $package);

            if ($package instanceof OptionsProvider) {
                $i[$packageName] = $package->getOptions();
            }
        }

        foreach ((array) $application->getOption('services') as $serviceName => $spec) {
            $i->nject($serviceName, $spec);
        }

        $i['application_config'] = $config;
        $i['config'] = $pm->getMergedConfig();

        $i->nject('dispatcher', new DispatcherFactory());
        $i->nject('request', Request::createFromGlobals());
        $i->nject('router', new RouterFactory());
        $i->nject('controller_manager', new ControllerManagerFactory());
        $i->nject('package_manager', $pm);
        $i->nject('view_manager', new ViewManager());

        $events = $application->events();
        $events->attach(new Listener\DispatchListener());
        $events->attach(new Listener\CreateViewModelListener());
        $events->attach(new Listener\InjectTemplateListener());
        $events->attach(new Listener\HandleErrorsListener());
        $events->attach(new Listener\RouteListener());
        $events->attach(new Listener\ResponseListener());
        $events->attach($i->nvoke('view_manager'));

        return $application;
    }
}
