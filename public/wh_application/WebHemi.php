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
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
defined('APPLICATION_PATH')
		|| define('APPLICATION_PATH', __DIR__);

defined('WEB_ROOT')
		|| define('WEB_ROOT', realpath(APPLICATION_PATH . '/../'));

defined('APPLICATION_ENV')
		|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once APPLICATION_PATH . '/module/WebHemi/src/WebHemi/Application.php';

/**
 * Run WebHemi Application
 */
WebHemi\Application::run();