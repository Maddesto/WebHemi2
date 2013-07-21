<?php

/**
 * WebHemi
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @category   WebHemi
 * @package    WebHemi
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */


defined('APPLICATION_PATH')
		|| define('APPLICATION_PATH', __DIR__);

defined('WEBHEMI_PATH')
		|| define('WEBHEMI_PATH', APPLICATION_PATH . '/module/WebHemi');

defined('WEB_ROOT')
		|| define('WEB_ROOT', realpath(APPLICATION_PATH . '/../'));

defined('APPLICATION_ENV')
		|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once WEBHEMI_PATH . '/src/WebHemi/Application.php';

defined('APPLICATION_MODULE')
		|| define('APPLICATION_MODULE', WebHemi\Application::getCurrentModuleFromUrl());

/**
 * Dump data
 *
 * @param  mixed   $var         The variable to dump
 * @param  string  $label       OPTIONAL Label to prepend to output
 * @param  bool    $echo        OPTIONAL Echo output if true
 * @param  bool    $backtrace   OPTIONAL Use bactrace to identify dump origin
 * @return string
 */
function dump($data, $label = null, $echo = true, $backtrace = true)
{
	\WebHemi\Application::getInstance()->varDump($data, $label, $echo, $backtrace);
}


/**
 * Run WebHemi Application
 */
WebHemi\Application::run();