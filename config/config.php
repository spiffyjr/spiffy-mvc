<?php

return [
    'spiffy.mvc' => [
        'services' => [
            'Spiffy\\Mvc\\View\\TwigStrategy' => 'Spiffy\\Mvc\\Factory\\TwigStrategyFactory',
        ],

        'view_manager' => [
            'strategies' => [
                'Spiffy\\Mvc\\View\\TwigStrategy',
            ],

            'not_found_template' => 'error/404',
            'error_template' => 'error/error',

            'twig' => [
                'suffix' => '.twig',
                'loader_paths' => [
                    'spiffy.mvc' => __DIR__ . '/../view'
                ],
                'options' => [
                    'cache' => 'cache'
                ]
            ]
        ],
    ]
];