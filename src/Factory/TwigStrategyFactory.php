<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Inject\Injector;
use Spiffy\Inject\ServiceFactory;
use Spiffy\Mvc\View\TwigStrategy;
use Spiffy\View\TwigRenderer;
use Spiffy\View\TwigResolver;

class TwigStrategyFactory implements ServiceFactory
{
    /**
     * @param Injector $i
     * @return TwigStrategy
     */
    public function createService(Injector $i)
    {
        $options = $i['spiffy.mvc']['view_manager']['twig'];

        $loader = new \Twig_Loader_Filesystem(array_reverse($options['loader_paths']));
        $twig = new \Twig_Environment($loader, $options['options']);

        $resolver = new TwigResolver($twig);
        $renderer = new TwigRenderer($twig, $resolver);
        $strategy = new TwigStrategy($renderer, $resolver);

        return $strategy;
    }
}
