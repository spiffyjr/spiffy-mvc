<?php

namespace Spiffy\Mvc\Factory;
use Spiffy\Inject\Injector;

/**
 * @coversDefaultClass \Spiffy\Mvc\Factory\TwigStrategyFactory
 */
class TwigStrategyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $i = new Injector();
        $i['spiffy.mvc'] = [
            'view_manager' => [
                'twig' => [
                    'loader_paths' => [],
                    'options' => []
                ]
            ]
        ];

        $df = new TwigStrategyFactory();

        /** @var \Spiffy\Mvc\View\TwigStrategy $result */
        $result = $df->createService($i);

        $this->assertInstanceOf('Spiffy\Mvc\View\TwigStrategy', $result);
    }
}
