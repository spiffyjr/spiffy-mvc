<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Inject\InjectorUtils;
use Spiffy\Mvc\Application;
use Spiffy\Mvc\Plugin;
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

        $i = new Injector();
        $pm = $this->createPackageManager($i, $config, $debug);

        /** @var \Spiffy\Mvc\Application $app */
        $app = $pm->getPackage('mvc');
        $app->setInjector($i);

        $this->injectServices($app, $pm);
        $this->injectConfig($app, $config);
        $this->injectEvents($app);
        $this->injectPlugins($app, $app->getInjector()['mvc']);

        return $app;
    }

    /**
     * @param Injector $i
     * @param array $config
     * @param bool $debug
     * @return PackageManager
     */
    protected function createPackageManager(Injector $i, array $config, $debug)
    {
        $tmp = isset($config['packages']) ? (array) $config['packages'] : [];
        $packages = ['mvc' => 'Spiffy\\Mvc\\Application'];
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
        $pm = $pmf->createService($config);
        $pm->events()->plug(new Plugin\PackageManager\MvcPlugin($i));
        $pm->load();

        return $pm;
    }

    /**
     * @param Application $app
     * @param array $applicationConfig
     */
    protected function injectConfig(Application $app, array $applicationConfig)
    {
        $i = $app->getInjector();
        $pm = $i->nvoke('package-manager');
        $mergedConfig = $pm->getMergedConfig();

        $i['application-config'] = $applicationConfig;
        $i['mvc'] = $mergedConfig['mvc'];
    }

    /**
     * @param Application $app
     */
    protected function injectEvents(Application $app)
    {
        $i = $app->getInjector();

        $events = $app->events();
        $events->plug(new Plugin\BootstrapPlugin());
        $events->plug(new Plugin\CreateViewModelPlugin());
        $events->plug(new Plugin\DispatchPlugin());
        $events->plug(new Plugin\HandleErrorsPlugin());
        $events->plug(new Plugin\InjectTemplatePlugin());
        $events->plug(new Plugin\ResponsePlugin());
        $events->plug(new Plugin\RoutePlugin());
        $events->plug($i->nvoke('view-manager'));
    }

    /**
     * @param Application $app
     * @param \Spiffy\Package\PackageManager $pm
     */
    protected function injectServices(Application $app, PackageManager $pm)
    {
        $i = $app->getInjector();

        $i->nject('dispatcher', new DispatcherFactory());
        $i->nject('injector', $i);
        $i->nject('request', Request::createFromGlobals());
        $i->nject('router', new RouterFactory());
        $i->nject('package-manager', $pm);
        $i->nject('view-manager', new ViewManagerFactory());
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
            $events->plug(InjectorUtils::get($i, $plugin));
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
