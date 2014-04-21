<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Mvc\Application;
use Spiffy\Mvc\Listener as MvcListener;
use Spiffy\Package\Feature\OptionsProvider;
use Spiffy\Package\PackageManager;
use Symfony\Component\HttpFoundation\Request;

class DefaultApplicationFactory
{
    /**
     * @param array $config
     * @return Application
     */
    public function create(array $config)
    {
        $this->injectEnvironment($config);

        $debug = isset($_ENV['debug']) && $_ENV['debug'] == true;

        $pm = $this->createPackageManager($config, $debug);
        $app = $pm->getPackage('spiffy.mvc');

        $this->injectServices($app, $pm);
        $this->injectConfig($app, $config);
        $this->injectPackages($app, $debug);
        $this->injectEvents($app);

        $this->injectPlugins($app, $app->getInjector()['spiffy.mvc']);

        return $app;
    }

    /**
     * @param array $config
     * @param bool $debug
     * @return PackageManager
     */
    protected function createPackageManager(array $config, $debug)
    {
        $tmp = isset($config['packages']) ? (array) $config['packages'] : [];
        $packages = ['spiffy.mvc' => 'Spiffy\\Mvc\\Application'];
        $config['packages'] = array_merge($packages, $tmp);

        foreach ($config['packages'] as $k => &$package) {
            if ($package[0] == '?') {
                if (!$debug) {
                    unset($config['packages'][$k]);
                    continue;
                }
                $package = substr($package, 1);
            }
        }

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
        $pm = $i->nvoke('package-manager');

        $i['application-config'] = $config;
        $i['config'] = $pm->getMergedConfig();
    }

    /**
     * @param Application $app
     */
    protected function injectEvents(Application $app)
    {
        $i = $app->getInjector();

        $events = $app->events();
        $events->attach(new MvcListener\DispatchListener());
        $events->attach(new MvcListener\CreateViewModelListener());
        $events->attach(new MvcListener\InjectTemplateListener());
        $events->attach(new MvcListener\HandleErrorsListener());
        $events->attach(new MvcListener\RouteListener());
        $events->attach(new MvcListener\ResponseListener());
        $events->attach($i->nvoke('view-manager'));
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
        $i->nject('package-manager', $pm);
        $i->nject('view-manager', new ViewManagerFactory());

        foreach ((array) $app->getOption('services') as $serviceName => $spec) {
            $i->nject($serviceName, $spec);
        }
    }

    /**
     * @param Application $app
     */
    protected function injectPackages(Application $app)
    {
        $i = $app->getInjector();

        /** @var \Spiffy\Package\PackageManager $pm */
        $pm = $i->nvoke('package-manager');

        // inject all packages (and options if they exist)
        foreach ($pm->getPackages() as $packageName => $package) {
            $i->nject($packageName, $package);

            if ($package instanceof OptionsProvider) {
                $i[$packageName] = $package->getOptions();
            }
        }
    }

    /**
     * @param Application $app
     * @param array $config
     */
    protected function injectPlugins(Application $app, array $config)
    {
        if (!isset($config['plugins'])) {
            return;
        }

        $events = $app->events();
        $i = $app->getInjector();

        foreach ($config['plugins'] as $plugin) {
            if (is_string($plugin)) {
                if ($i->has($plugin)) {
                    $plugin = $i->get($plugin);
                } else {
                    $plugin = new $plugin;
                }
            }

            $events->attach($plugin);
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
