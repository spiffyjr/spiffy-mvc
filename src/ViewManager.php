<?php

namespace Spiffy\Mvc;

use Spiffy\Event\Plugin;
use Spiffy\Event\Manager;
use Spiffy\Inject\Injector;
use Spiffy\Inject\InjectorUtils;
use Spiffy\Mvc\MvcEvent;
use Spiffy\View\Model;
use Spiffy\View\Strategy;
use Symfony\Component\HttpFoundation\Response;

class ViewManager implements Plugin
{
    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @var Strategy
     */
    protected $defaultStrategy;

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
     * @param Strategy $defaultStrategy
     */
    public function __construct(Strategy $defaultStrategy)
    {
        $this->defaultStrategy = $defaultStrategy;
    }

    /**
     * @param Manager $events
     * @return void
     */
    public function plug(Manager $events)
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
        $i = $this->injector = $app->getInjector();

        $options = $i['mvc']['view_manager'];

        if (isset($options['not_found_template'])) {
            $this->notFoundTemplate = $options['not_found_template'];
        }
        if (isset($options['error_template'])) {
            $this->errorTemplate = $options['error_template'];
        }

        if (isset($options['strategies'])) {
            $this->strategies = (array) $options['strategies'];
            $this->registerStrategies($app->events());
        }
    }

    /**
     * @param MvcEvent $e
     * @return null|string
     */
    public function onRender(MvcEvent $e)
    {
        // If the response is set we assume the request is finished and short-circuit.
        //if ($e->getResponse() instanceof Response) {
            //return;
        //}

        $model = $e->getModel();
        if (!$model instanceof Model) {
            return;
        }

        $result = null;
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

                break;
            }

            if (null !== $result) {
                break;
            }
        }

        if (null === $result) {
            $result = $this->defaultStrategy->render($model);
        }

        $e->setRenderResult($result);
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
    protected function registerStrategies(Manager $events)
    {
        $i = $this->injector;
        foreach ($this->strategies as $index => &$strategy) {
            if (!is_string($strategy)) {
                unset($this->strategies[$index]);
                continue;
            }

            $strategy = InjectorUtils::get($i, $strategy);

            if (!$strategy instanceof Strategy) {
                unset($this->strategies[$index]);
            }

            if ($strategy instanceof Plugin) {
                $events->plug($strategy);
            }
        }
    }
}
