<?php

namespace Spiffy\Mvc\TestAsset;

use Spiffy\Mvc\AbstractController;

class TestController extends AbstractController
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
