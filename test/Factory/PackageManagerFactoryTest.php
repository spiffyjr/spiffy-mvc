<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\PackageManagerFactory
 */
class PackageManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createService, createPackageManager
     */
    public function testCreateService()
    {
        $config = [
            'packages' => [
                'spiffy.package.test-asset.application',
            ]
        ];

        $pm = new PackageManagerFactory();

        /** @var \Spiffy\Package\PackageManager $result */
        $pm = $pm->createService($config);

        $this->assertInstanceOf('Spiffy\Package\PackageManager', $pm);
        $this->assertCount(1, $pm->getPackages());
        $this->assertInstanceOf(
            'Spiffy\Package\TestAsset\Application\Package',
            $pm->getPackage('spiffy.package.test-asset.application')
        );
    }

    /**
     * @covers ::createService, createPackageManager
     */
    public function testCreateServiceWithFcqnPackages()
    {
        $config = [
            'packages' => [
                'spiffy.package.test-asset.application',
                'spiffy.package.test-asset.fqcn' => 'Spiffy\\Package\\TestAsset\\FQCN\\Module'
            ]
        ];

        $pm = new PackageManagerFactory();

        /** @var \Spiffy\Package\PackageManager $result */
        $pm = $pm->createService($config);

        $this->assertInstanceOf('Spiffy\Package\PackageManager', $pm);
        $this->assertCount(2, $pm->getPackages());
        $this->assertInstanceOf(
            'Spiffy\Package\TestAsset\Application\Package',
            $pm->getPackage('spiffy.package.test-asset.application')
        );
        $this->assertInstanceOf(
            'Spiffy\\Package\\TestAsset\\FQCN\\Module',
            $pm->getPackage('spiffy.package.test-asset.fqcn')
        );
    }

    /**
     * @covers ::createService, createPackageManager
     */
    public function testCreateServiceOverridesFlagsAndPattern()
    {
        $config = [
            'packages' => [],
            'override_pattern' => __DIR__ . '/../config/config.php',
            'override_flags' => GLOB_BRACE
        ];

        $pm = new PackageManagerFactory();

        /** @var \Spiffy\Package\PackageManager $result */
        $pm = $pm->createService($config);
        $this->assertInstanceOf('Spiffy\Package\PackageManager', $pm);

        /** @var \Spiffy\Package\PackageManager $pm */
        $this->assertSame($config['override_pattern'], $pm->getOverridePattern());
        $this->assertSame($config['override_flags'], $pm->getOverrideFlags());
    }
}
