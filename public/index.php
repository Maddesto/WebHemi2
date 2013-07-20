<?php

//$dir = __DIR__ . '/wh_application/vendor/Zend/I18n/Validator/PhoneNumber/';
//$codes = array();
//$d = dir($dir);
//while (false !== ($entry = $d->read())) {
//	if (strpos($entry, '.php') !== false) {
//		$data = include_once  $dir . $entry;
//		$code = $data['code'];
//		if (isset($codes[$code[0]][$code])) {
//			$codes[$code[0]][$code] = '-';
//		}
//		else {
//			$codes[$code[0]][$code] = str_replace('.php', '',$entry);
//		}
//	}
//}
//$d->close();
//
//$tmp = array();
//for ($i = 1; $i <= 9; $i++) {
//	$tmp[$i] = array();
//	for ($j = 1; $j < 1000; $j++) {
//		if (isset($codes[$i][$j])) {
//			$tmp[$i][$j] = $codes[$i][$j];
//		}
//	}
//}
//
//echo '<pre>';
//var_export($tmp);
//exit;
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
