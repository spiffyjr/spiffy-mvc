<?php

namespace Spiffy\Mvc\Plugin;

use Spiffy\Event\Plugin;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Symfony\Component\HttpFoundation\Response;

class ResponsePlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(MvcEvent::EVENT_FINISH, [$this, 'sendResponse']);
    }

    /**
     * @param MvcEvent $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendResponse(MvcEvent $e)
    {
        $response = $e->getResponse();

        if (!$response instanceof Response) {
            $response = new Response();
        }

        if ($e->getRenderResult()) {
            $response->setContent($e->getRenderResult());
        }
        $response->send();

        return $response;
    }
}
