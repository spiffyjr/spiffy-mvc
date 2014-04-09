<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\DispatcherFactory
 */
class DispatcherFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $i = new Injector();
        $i['spiffy.mvc'] = [
            'controllers' => [
                'test' => 'Spiffy\Mvc\TestAsset\TestController',
            ]
        ];

        $df = new DispatcherFactory();

        /** @var \Spiffy\Dispatch\Dispatcher $result */
        $result = $df->createService($i);

        $this->assertInstanceOf('Spiffy\Dispatch\Dispatcher', $result);
        $this->assertTrue($result->has('test'));
    }
}
