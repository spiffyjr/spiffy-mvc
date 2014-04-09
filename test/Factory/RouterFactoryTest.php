<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\RouterFactory
 */
class RouterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $i = new Injector();
        $i['spiffy.mvc'] = [
            'routes' => [
                'home' => ['/', 'home', 'index'],
                'defaults' => ['/defaults', 'defaults', 'index', ['foo' => 'bar']]
            ]
        ];

        $df = new RouterFactory();

        /** @var \Spiffy\Route\Router $result */
        $result = $df->createService($i);

        $this->assertInstanceOf('Spiffy\Route\Router', $result);
        $this->assertCount(2, $result->getRoutes());

        $match = $result->match('/defaults');
        $this->assertInstanceOf('Spiffy\Route\RouteMatch', $match);
        $this->assertSame('bar', $match->get('foo'));
    }
}
