<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\DefaultApplicationFactory
 */
class DefaultApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createService, ::__construct
     */
    public function testCreateService()
    {
        $df = new DefaultApplicationFactory([]);

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->createService();
        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
    }

    /**
     * @covers ::createService, createPackageManager
     */
    public function testCreateServiceOverridesFlagsAndPattern()
    {
        $config = [
            'override_pattern' => __DIR__ . '/../config/config.php',
            'override_flags' => GLOB_BRACE
        ];

        $df = new DefaultApplicationFactory($config);

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->createService();
        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);

        $i = $result->getInjector();

        /** @var \Spiffy\Package\PackageManager $pm */
        $pm = $i->nvoke('package_manager');
        $this->assertSame($config['override_pattern'], $pm->getOverridePattern());
        $this->assertSame($config['override_flags'], $pm->getOverrideFlags());
    }

    /**
     * @covers ::createService, injectEnvironment
     */
    public function testCreateServiceSetsEnvVariables()
    {
        $config = [
            'environment' => [
                'foo' => 'bar'
            ]
        ];

        $df = new DefaultApplicationFactory($config);

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->createService();
        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
        $this->assertSame('bar', $_ENV['foo']);
    }

    /**
     * @covers ::createService, injectEnvironment
     */
    public function testCreateServiceInjectsConfig()
    {
        $config = [
            'environment' => [
                'foo' => 'bar'
            ]
        ];

        $df = new DefaultApplicationFactory($config);

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->createService();
        $i = $result->getInjector();

        $this->assertInstanceOf('Spiffy\Mvc\Application', $result);
        $this->assertSame($config, $i['application_config']);
        $this->assertSame($i->nvoke('package_manager')->getMergedConfig(), $i['config']);
    }
}
