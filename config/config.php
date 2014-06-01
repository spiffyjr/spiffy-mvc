<?php

return [
    'mvc' => [
        'name' => 'Spiffy\Mvc',
        'version' => \Spiffy\Mvc\Version::CURRENT,
        'decorators' => [],
        'services' => [],
        'wrappers' => [],
        'view_manager' => [
            /*
             * This component does little on its own without an extending
             *  component to supply the default strategy. spiffy.mvc.twig
             * is an example.
             */
            'default_strategy' => 'Spiffy\Mvc\EmptyViewStrategy',

            'strategies' => ['Spiffy\View\JsonStrategy'],

            'not_found_template' => 'error/404',
            'error_template' => 'error/error',
        ],
    ]
];
