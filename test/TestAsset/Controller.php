<?php

namespace Spiffy\Mvc\TestAsset;

use Spiffy\Mvc\AbstractController;

class Controller extends AbstractController
{
    public function index()
    {
        return 'index';
    }

    public function camelCase()
    {
        return 'camelCase';
    }
}
