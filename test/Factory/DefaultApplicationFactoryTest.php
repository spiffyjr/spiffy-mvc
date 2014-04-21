<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Mvc\MvcEvent;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\DefaultApplicationFactory
 */
class DefaultApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::create, ::__construct
     */
    public function testCreateService()
    {
        $df = new DefaultApplicationFactory();

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->create([]);
        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
    }

    /**
     * @covers ::injectPlugins
     */
    public function testInjectPlugins()
    {
        $df = new DefaultApplicationFactory();
        $result = $df->create([]);
        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);

        $result = $df->create(['packages' => 'spiffy.mvc.test-asset.application']);
        $events = $result->events()->getEvents(MvcEvent::EVENT_DISPATCH);
        /** @var \Closure $plugin */
        $plugin = $events->extract();

        $this->assertInstanceOf('Closure', $plugin);
        $this->assertSame('test', $plugin());

        /** @var \Closure $plugin2 */
        $plugin2 = $events->extract();
        $this->assertInstanceOf('Closure', $plugin2);
        $this->assertSame('testtwo', $plugin2());
    }

    /**
     * @covers ::injectServices
     */
    public function testInjectServices()
    {
        $df = new DefaultApplicationFactory();
        $result = $df->create(['packages' => 'spiffy.mvc.test-asset.application']);
        $i = $result->getInjector();

        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
        $this->assertInstanceOf('StdClass', $i->nvoke('stdclass'));
    }

    /**
     * @covers ::createPackageManager, ::create
     */
    public function testCreatePackageManagerSkipsDebugPackages()
    {
        $config = [
            'environment' => ['debug' => false],
            'packages' => [
                'spiffy.package.test-asset.application',
                'spiffy.package.test-asset.options',
                '?spiffy.package.test-asset.path',
            ]
        ];

        $df = new DefaultApplicationFactory();
        $result = $df->create($config);
        $pm = $result->getInjector()->nvoke('package-manager');

        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);

        // spiffy.mvc is registered so count is three, not two.
        $this->assertCount(3, $pm->getPackages());

        $config['environment']['debug'] = true;
        $result = $df->create($config);
        $pm = $result->getInjector()->nvoke('package-manager');

        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
        $this->assertCount(4, $pm->getPackages());
    }

    /**
     * @covers ::create, ::createPackageManager
     */
    public function testCreateServiceOverridesFlagsAndPattern()
    {
        $config = [
            'override_pattern' => __DIR__ . '/../config/config.php',
            'override_flags' => GLOB_BRACE
        ];

        $df = new DefaultApplicationFactory();

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->create($config);
        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);

        $i = $result->getInjector();

        /** @var \Spiffy\Package\PackageManager $pm */
        $pm = $i->nvoke('package-manager');
        $this->assertSame($config['override_pattern'], $pm->getOverridePattern());
        $this->assertSame($config['override_flags'], $pm->getOverrideFlags());
    }

    /**
     * @covers ::create, ::injectEnvironment
     */
    public function testCreateServiceSetsEnvVariables()
    {
        $config = [
            'environment' => [
                'foo' => 'bar'
            ]
        ];

        $df = new DefaultApplicationFactory();

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->create($config);
        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
        $this->assertSame('bar', $_ENV['foo']);
    }

    /**
     * @covers ::create, ::injectEnvironment
     */
    public function testCreateServiceInjectsConfig()
    {
        $config = [
            'environment' => [
                'foo' => 'bar'
            ]
        ];

        $df = new DefaultApplicationFactory();

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->create($config);
        $i = $result->getInjector();

        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
        $this->assertSame($config, $i['application-config']);
        $this->assertSame($i->nvoke('package-manager')->getMergedConfig(), $i['config']);
    }
}
