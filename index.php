<?php

/**
 * WebHemi2
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
 * @category   WebHemi2
 * @package    WebHemi2
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'live'));

if ('dev' == APPLICATION_ENV) {
    require_once(__DIR__ . '/wh_application/library/dump.php');
}
require_once(__DIR__ . '/wh_application/module/WebHemi2/resources/application_constants.php');

$applicationConfigFile = APPLICATION_PATH . '/config/application.config.php';

if (file_exists($applicationConfigFile)) {
    $applicationConfig = include($applicationConfigFile);
} else {
    $applicationConfig = array(
        APPLICATION_MODULE => array(
            'type' => APPLICATION_MODULE_TYPE,
            'path' => APPLICATION_MODULE_URI,
            'wh_themes' => array(
                'current_theme' => 'default',
            ),
        )
    );
}

$theme = $applicationConfig[APPLICATION_MODULE]['wh_themes']['current_theme'];

$commonConfig = include(APPLICATION_PATH . '/config/common.module.config.php');
$errorTemplate = $commonConfig['view_manager']['exception_template'];

if ($theme == 'default') {
    $themePath = APPLICATION_PATH . '/resources/default';
} else {
    $themePath = APPLICATION_PATH . '/resources/themes/' . $theme;
    $templateConfig = include($themePath . '/theme.config.php');
    $errorTemplate = $templateConfig['exception_template'];
}

include $themePath . '/view/' . $errorTemplate . '.phtml';