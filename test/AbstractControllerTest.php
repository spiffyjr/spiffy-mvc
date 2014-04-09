<?php

namespace Spiffy\Mvc;

use Spiffy\Mvc\TestAsset\TestController;
use Spiffy\View\ViewModel;

/**
 * @coversDefaultClass \Spiffy\Mvc\AbstractController
 */
class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::notFound
     */
    public function testNotFoundMethodReturnsEmptyViewModel()
    {
        $c = new TestController();
        $this->assertEquals(new ViewModel(), $c->notFound());
    }

    /**
     * @covers ::dispatch, \Spiffy\Mvc\Exception\MissingMvcEventException::__construct
     * @expectedException \Spiffy\Mvc\Exception\MissingMvcEventException
     * @expectedExceptionMessage Failed to dispatch: missing action but no MvcEvent available.
     */
    public function testDispatchThrowsExceptionOnMissingActionWithNoMvcEvent()
    {
        $c = new TestController();
        $c->dispatch(['action' => 'doesnotexist']);
    }

    /**
     * @covers ::dispatch
     */
    public function testDispatchFiresDispatchErrorOnMissingAction()
    {
        $result = 'error';
        $app = new Application();
        $events = $app->events();
        $events->on(MvcEvent::EVENT_DISPATCH_ERROR, function() use ($result) {
            return $result;
        });
        $c = new TestController();

        $params = ['event' => new MvcEvent($app), 'action' => 'doesnotexist'];
        $this->assertSame($result, $c->dispatch($params));
    }

    /**
     * @covers ::dispatch, ::normalize
     */
    public function testDispatchNormalizesActions()
    {
        $c = new TestController();
        $params = ['action' => 'camel-case'];
        $this->assertSame('camelCase', $c->dispatch($params));
    }

    /**
     * @covers ::dispatch
     */
    public function testDefaultActionIsIndex()
    {
        $c = new TestController();
        $this->assertSame('index', $c->dispatch([]));
    }
}
