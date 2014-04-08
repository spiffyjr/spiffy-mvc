<?php

namespace Spiffy\Mvc\View;

use Spiffy\Event\Listener;
use Spiffy\Event\Manager;
use Spiffy\Inject\Injector;
use Spiffy\Mvc\MvcEvent;

class ViewManager implements Listener
{
    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @var string
     */
    protected $notFoundTemplate = 'error/404';

    /**
     * @var string
     */
    protected $errorTemplate = 'error/error';

    /**
     * @var \Spiffy\View\Strategy[]
     */
    protected $strategies = [];

    /**
     * @param Manager $events
     * @return void
     */
    public function attach(Manager $events)
    {
        $events->on(MvcEvent::EVENT_BOOTSTRAP, [$this, 'onBootstrap']);
        $events->on(MvcEvent::EVENT_RENDER, [$this, 'onRender']);
    }

    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();

        $this->injector = $app->getInjector();

        $options = $app->getOption('view_manager');
        $this->notFoundTemplate = $options['not_found_template'];
        $this->errorTemplate = $options['error_template'];
        $this->strategies = (array) $options['strategies'];

        $this->registerStrategies();
    }

    /**
     * @param MvcEvent $e
     */
    public function onRender(MvcEvent $e)
    {
        $model = $e->getViewModel();

        foreach ($this->strategies as $strategy) {
            if (!$strategy->canRender($model)) {
                continue;
            }

            try {
                $result = $strategy->render($model);
            } catch (\Exception $ex) {
                $e->setError(MvcEvent::ERROR_EXCEPTION);
                $e->setType(MvcEvent::EVENT_RENDER_ERROR);
                $e->set('exception', $ex);
                $e->getApplication()->events()->fire($e);

                $model->setTemplate($this->getErrorTemplate());
                $model->setVariables($e->getParams());

                $result = $strategy->render($model);
            }

            $e->setResult($result);
        }
    }

    /**
     * @param string $notFoundTemplate
     */
    public function setNotFoundTemplate($notFoundTemplate)
    {
        $this->notFoundTemplate = $notFoundTemplate;
    }

    /**
     * @return string
     */
    public function getNotFoundTemplate()
    {
        return $this->notFoundTemplate;
    }

    /**
     * @param string $errorTemplate
     */
    public function setErrorTemplate($errorTemplate)
    {
        $this->errorTemplate = $errorTemplate;
    }

    /**
     * @return string
     */
    public function getErrorTemplate()
    {
        return $this->errorTemplate;
    }

    /**
     * Register strategies.
     */
    protected function registerStrategies()
    {
        $i = $this->injector;
        foreach ($this->strategies as $index => &$strategy) {
            if (!is_string($strategy)) {
                continue;
            }

            if ($i->has($strategy)) {
                $strategy = $i->nvoke($strategy);
            } else {
                $strategy = new $strategy();
            }

            if ($strategy instanceof Listener) {
                unset($this->strategies[$index]);
            }
        }
    }
}
