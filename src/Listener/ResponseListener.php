<?php

namespace Spiffy\Mvc\Listener;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Mvc\MvcEvent;
use Symfony\Component\HttpFoundation\Response;

class ResponseListener implements Listener
{
    /**
     * {@inheritDoc}
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_FINISH, [$this, 'sendResponse']);
    }

    /**
     * @param MvcEvent $e
     */
    public function sendResponse(MvcEvent $e)
    {
        $response = $e->getResponse();

        if (!$response instanceof Response) {
            $response = new Response();
        }

        $response->setContent($e->getResult());
        $response->send();
    }
}
