<?php

//$x = file_get_contents('dialog-warning.png');
//die(base64_encode($x));

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
