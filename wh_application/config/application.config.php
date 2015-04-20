<?php
return [
    'modules' => [
        'WebHemi2',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './wh_application/module',
            './wh_application/vendor',
        ],
        'config_glob_paths' => [
            'wh_application/config/autoload/{,*.}{global,local}.php',
        ],
    ],
];
