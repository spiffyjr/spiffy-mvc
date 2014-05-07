<?php

namespace Spiffy\Mvc;

use Spiffy\Inject\Injector;
use Spiffy\Mvc\TestAsset\Application\Command\TestCommand;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Mvc\ConsoleCommand
 */
class ConsoleCommmandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getInjector, ::setInjector
     */
    public function testSetGetInjector()
    {
        $command = new TestCommand();
        $i = new Injector();

        $command->setInjector($i);
        $this->assertSame($i, $command->getInjector());
    }
}
