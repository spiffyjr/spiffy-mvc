<?php

namespace Spiffy\Mvc;

use Spiffy\Mvc\TestAsset\Application\Command\TestCommand;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Spiffy\Mvc\ConsoleApplication
 */
class ConsoleApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct, ::getInjector, ::getApplication
     */
    public function testGetInjectorGetApplication()
    {
        $app = new Application();
        $console = new ConsoleApplication($app);

        $this->assertSame($app->getInjector(), $console->getInjector());
        $this->assertSame($app, $console->getApplication());
    }

    /**
     * @covers ::add
     */
    public function testInjectingConsoleCommands()
    {
        $app = new Application();
        $console = new ConsoleApplication($app);
        $command = new TestCommand();

        $this->assertSame($command, $console->add($command));
        $this->assertSame($app->getInjector(), $command->getInjector());
    }
}
