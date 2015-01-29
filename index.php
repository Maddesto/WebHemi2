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
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

// define environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'live'));
// allow debug for development
if ('dev' == APPLICATION_ENV) {
    require_once(__DIR__ . '/wh_application/library/dump.php');
}
// create applitacion-wide constants
require_once(__DIR__ . '/wh_application/module/WebHemi2/resources/application_constants.php');
// load application config to get template info
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

// include common config to get the error template
$commonConfig = include(APPLICATION_PATH . '/config/common.module.config.php');
$errorTemplate = $commonConfig['view_manager']['exception_template'];

if ($theme == 'default') {
    $themePath = APPLICATION_PATH . '/resources/default';
} else {
    $themePath = APPLICATION_PATH . '/resources/themes/' . $theme;
    // include theme config to get the theme error template if defined
    $templateConfig = include($themePath . '/theme.config.php');
    $errorTemplate = $templateConfig['exception_template'];
}

include $themePath . '/view/' . $errorTemplate . '.phtml';