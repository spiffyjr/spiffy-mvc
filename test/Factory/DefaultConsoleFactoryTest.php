<?php

namespace Spiffy\Mvc\Factory;

use Mockery as m;
use Symfony\Component\Finder\Finder;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\DefaultConsoleFactoryTest
 */
class DefaultConsoleFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::create, ::injectCommands, ::createFinder, ::addCommands
     */
    public function testCreateServiceInjectsCommands()
    {
        $df = new DefaultConsoleFactory();

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->create([
            'packages' => [
                'mvc.test-asset.application',
            ]
        ]);
        $this->assertInstanceOf('Spiffy\Mvc\ConsoleApplication', $result);
    }

    /**
     * @covers ::create, ::__construct
     */
    public function testCreateService()
    {
        $df = new DefaultConsoleFactory();

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->create([]);
        $this->assertInstanceOf('Spiffy\Mvc\ConsoleApplication', $result);
    }

    /**
     * @covers ::create, ::injectCommands
     */
    public function testCreateServiceCatchesLogicException()
    {
        $df = m::mock('Spiffy\Mvc\Factory\DefaultConsoleFactory[createFinder]');
        $df->shouldAllowMockingProtectedMethods(true);
        $df
            ->shouldReceive('createFinder')
            ->once()
            ->andReturn(new Finder());

        $result = $df->create([]);
        $this->assertInstanceOf('Spiffy\Mvc\ConsoleApplication', $result);
    }
}
