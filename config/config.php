<?php

return [
    'spiffy.mvc' => [
        'name' => 'Spiffy\Mvc',
        'version' => \Spiffy\Mvc\Version::CURRENT,
        'services' => [],
        'view_manager' => [
            // This component does little on its own without an extending
            // component to supply the default strategy. spiffy-mvc-twig
            // is an example.
            'default_strategy' => 'Spiffy\Mvc\EmptyViewStrategy',

            'strategies' => [],

            'not_found_template' => 'error/404',
            'error_template' => 'error/error',
        ],
    ]
];
