<?php

return array(
	'Admin' => array(
		'type'       => 'subdomain',
		'path'       => 'admin',
		'translator' => array(
			'locale' => 'en_US',
		),
	),
	'Website' => array(
		'type'       => 'subdomain',
		'path'       => 'www',
		'translator' => array(
			'locale' => 'hu_HU',
		),
		'wh_themes' => array(
//			'current_theme' => 'gold',
		),
	),
	'AdminWiki' => array(
		'type'       => 'subdomain',
		'path'       => 'admin.wiki',
		'translator' => array(
			'locale' => 'en_US',
		),
		'wh_themes' => array(
			'current_theme' => 'silver',
		),
	),
);
