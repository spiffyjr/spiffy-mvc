<?php

namespace Spiffy\Mvc\View;

use Spiffy\View\Renderer;
use Spiffy\View\Resolver;
use Spiffy\View\Strategy;

class TwigStrategy implements Strategy
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @param Renderer $renderer
     * @param Resolver $resolver
     */
    public function __construct(Renderer $renderer, Resolver $resolver)
    {
        $this->renderer = $renderer;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function render($nameOrModel)
    {
        return $this->renderer->render($nameOrModel);
    }

    /**
     * This is the default MVC strategy so we always return true. In situations
     * where the twig tempalte does not exist the error page should be handled properly
     * by the HandleErrorsListener.
     *
     * {@inheritDoc}
     */
    public function canRender($nameOrModel)
    {
        return true;
    }
}
