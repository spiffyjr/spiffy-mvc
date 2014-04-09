<?php

namespace Spiffy\Mvc\TestAsset;

use Spiffy\View\Strategy;

class ExceptionStrategy implements Strategy
{
    /**
     * @param string $nameOrModel
     * @return bool
     */
    public function canRender($nameOrModel)
    {
        return true;
    }

    /**
     * @param string $nameOrModel
     * @param array $variables
     * @throws \RuntimeException
     * @return string
     */
    public function render($nameOrModel, array $variables = [])
    {
        throw new \RuntimeException();
    }
}
