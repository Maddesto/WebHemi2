<?php

if ('development' == getenv('APPLICATION_ENV')) {
	$start = processTime(microtime());
}

/**
 * PHP version check
 */
if (!function_exists('version_compare') || version_compare('5.3', phpversion()) > 0) {
	trigger_error('Your PHP version is below required!', E_USER_ERROR);
	exit;
}

/**
 * Execute WebHemi. You don't have to do anything else from now :)
 */
require_once __DIR__ . '/wh_application/WebHemi.php';

function processTime($microtime) {
	list($mic, $sec) = explode(' ', $microtime);
	return (float)$sec + (float)$mic;
}

if ('development' == getenv('APPLICATION_ENV')) {
	$stop = processTime(microtime());
	// the +7 msec is the average Apache response time according to the Firebug
//	echo 'Total: ' . number_format(($stop - $start + 0.007), 3, '.', '') . ' sec';
}