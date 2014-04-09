<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Mvc\Application;
use Spiffy\Mvc\Listener;
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

        $this->injectEnvironment($config);
        $pm = $this->createPackageManager($config);

        $app = $pm->getPackage('spiffy.mvc');

        $this->injectServices($app, $pm);
        $this->injectConfig($app, $config);
        $this->injectPackages($app);
        $this->injectEvents($app);

        return $app;
    }

    /**
     * @param array $config
     * @return PackageManager
     */
    protected function createPackageManager(array $config)
    {
        $tmp = isset($config['packages']) ? (array) $config['packages'] : [];
        $packages = ['spiffy.mvc' => 'Spiffy\\Mvc\\Application'];
        $config['packages'] = array_merge($packages, $tmp);

        $pmf = new PackageManagerFactory();
        return $pmf->createService($config);
    }

    /**
     * @param Application $app
     * @param array $config
     */
    protected function injectConfig(Application $app, array $config)
    {
        $i = $app->getInjector();
        $pm = $i->nvoke('package_manager');

        $i['application_config'] = $config;
        $i['config'] = $pm->getMergedConfig();
    }

    /**
     * @param Application $app
     */
    protected function injectEvents(Application $app)
    {
        $i = $app->getInjector();

        $events = $app->events();
        $events->attach(new Listener\DispatchListener());
        $events->attach(new Listener\CreateViewModelListener());
        $events->attach(new Listener\InjectTemplateListener());
        $events->attach(new Listener\HandleErrorsListener());
        $events->attach(new Listener\RouteListener());
        $events->attach(new Listener\ResponseListener());
        $events->attach($i->nvoke('view_manager'));
    }

    /**
     * @param Application $app
     * @param \Spiffy\Package\PackageManager $pm
     */
    protected function injectServices(Application $app, PackageManager $pm)
    {
        $i = $app->getInjector();

        $i->nject('dispatcher', new DispatcherFactory());
        $i->nject('request', Request::createFromGlobals());
        $i->nject('router', new RouterFactory());
        $i->nject('package_manager', $pm);
        $i->nject('view_manager', new ViewManagerFactory());
    }

    /**
     * @param Application $app
     */
    protected function injectPackages(Application $app)
    {
        $i = $app->getInjector();

        /** @var \Spiffy\Package\PackageManager $pm */
        $pm = $i->nvoke('package_manager');

        /** @var \Spiffy\Mvc\Application $app */
        $app = $pm->getPackage('spiffy.mvc');

        // inject all packages (and options if they exist)
        foreach ($pm->getPackages() as $packageName => $package) {
            $i->nject($packageName, $package);

            if ($package instanceof OptionsProvider) {
                $i[$packageName] = $package->getOptions();
            }
        }

        foreach ((array) $app->getOption('services') as $serviceName => $spec) {
            $i->nject($serviceName, $spec);
        }
    }

    /**
     * @param array $config
     */
    protected function injectEnvironment(array $config)
    {
        $env = isset($config['environment']) ? (array) $config['environment'] : [];
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}
