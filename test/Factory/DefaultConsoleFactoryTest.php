<?php

namespace Spiffy\Mvc\Factory;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\DefaultConsoleFactoryTest
 */
class DefaultConsoleFactoryTest extends \PHPUnit_Framework_TestCase
{
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
    public function testCreateServiceInjectsCommands()
    {
        $df = new DefaultConsoleFactory();

        /** @var \Spiffy\Mvc\Application $result */
        $result = $df->create([
            'packages' => 'spiffy.mvc.test-asset.application'
        ]);
        $this->assertInstanceOf('Spiffy\Mvc\ConsoleApplication', $result);
    }
}
