<?php

return array(
	'display_not_found_reason' => true,
	'display_exceptions'       => true,
	'doctype'                  => 'HTML5',
	'not_found_template'       => 'error/404',
	'exception_template'       => 'error/index',
	'template_path_stack'      => array(
		'silver' => __DIR__ . '/view',
	),
	'template_map'               => array(
		'layout/layout'          => __DIR__ . '/view/layout.phtml',
		'layout/default'         => __DIR__ . '/view/layout.phtml',
		'web-hemi/website/index' => __DIR__ . '/view/index.phtml',
		'web-hemi/website/view'  => __DIR__ . '/view/view.phtml',
		'web-hemi/user/index'    => __DIR__ . '/view/user/index.phtml',
		'web-hemi/user/login'    => __DIR__ . '/view/user/login.phtml',
		'error/403'              => __DIR__ . '/view/error/403.phtml',
		'error/404'              => __DIR__ . '/view/error/404.phtml',
		'error/index'            => __DIR__ . '/view/error/500.phtml',
	),
);
