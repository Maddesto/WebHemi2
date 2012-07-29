<?php

return array(
	'modules' => array(
        'WebHemi',
    ),
	'module_listener_options' => array(
		'config_glob_paths' => array(
			__DIR__ . '/autoload/{,*.}{global,local}.php',
		),
		'module_paths' => array(
			__DIR__ . '/../module',
		),
	),
);
