<?php

namespace Spiffy\Mvc;

use Spiffy\View\Strategy;

class EmptyViewStrategy implements Strategy
{
    /**
     * @param string $nameOrModel
     * @return bool
     */
    public function canRender($nameOrModel)
    {
        return false;
    }

    /**
     * @param string $nameOrModel
     * @param array $variables
     * @return string
     */
    public function render($nameOrModel, array $variables = [])
    {
        return '';
    }
}
