<?php

namespace Spiffy\Mvc\TestAsset\Application;

use Spiffy\Mvc\TestAsset\TestPluginTwo;
use Spiffy\Package\Feature\ConfigProvider;
use Spiffy\Package\Feature\PathProvider;

class Package implements ConfigProvider, PathProvider
{
    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return [
            'mvc' => [
                'plugins' => [
                    'Spiffy\Mvc\TestAsset\TestPlugin',
                    'plugin',
                ],
                'services' => [
                    'plugin' => new TestPluginTwo(),
                    'stdclass' => new \StdClass()
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return __DIR__;
    }
}
