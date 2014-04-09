<?php

return [
    'spiffy.mvc' => [
        'services' => [
            'Spiffy\\Mvc\\View\\TwigStrategy' => 'Spiffy\\Mvc\\Factory\\TwigStrategyFactory',
        ],

        'view_manager' => [
            'default_strategy' => 'Spiffy\\Mvc\\View\\TwigStrategy',

            'strategies' => [],

            'not_found_template' => 'error/404',
            'error_template' => 'error/error',

            'twig' => [
                'suffix' => '.twig',
                'loader_paths' => [
                    'spiffy.mvc' => __DIR__ . '/../view'
                ],
                'options' => []
            ]
        ],
    ]
];
