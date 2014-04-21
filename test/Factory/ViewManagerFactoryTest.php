<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Mvc\TestAsset\TestStrategy;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\ViewManagerFactory
 */
class ViewManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $i = new Injector();
        $i['spiffy.mvc'] = [
            'view_manager' => [
                'default_strategy' => 'Spiffy\Mvc\TestAsset\TestStrategy'
            ]
        ];
        $i->nject('Spiffy\Mvc\TestAsset\TestStrategy', new TestStrategy());

        $df = new ViewManagerFactory();

        /** @var \Spiffy\Mvc\ViewManager $result */
        $result = $df->createService($i);

        $this->assertInstanceOf('Spiffy\Mvc\ViewManager', $result);
    }
}
