<?php
return array(
    'modules' => array(
        'WebHemi2',
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './wh_application/module',
            './wh_application/vendor',
        ),
        'config_glob_paths' => array(
            'wh_application/config/autoload/{,*.}{global,local}.php',
        ),
    ),
);
