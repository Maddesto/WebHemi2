<?php

/**
 * WebHemi2
 *
 *
 * PHP version 5.4
 *
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
 * @category  WebHemi2
 * @package   WebHemi2
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

/**
 * If there is rewrite enabled for the webserver the primary entry point will be the application.php.
 *
 * If there is no rewrite enabled for the webserver this file - as the default entry point - will provide an error page.
 *
 */
// create applitacion-wide constants
require_once(__DIR__ . '/wh_application/library/application_constants.php');

// load application config to get template info
$applicationConfigFile = APPLICATION_MODULE_PATH . '/config/application.config.php';

if (file_exists($applicationConfigFile)) {
    $applicationConfig = include($applicationConfigFile);
} else {
    $applicationConfig = array(
        APPLICATION_MODULE => array(
            'type' => APPLICATION_MODULE_TYPE,
            'path' => APPLICATION_MODULE_URI,
            'view_themes' => array(
                'current_theme' => 'default',
            ),
        )
    );
}

$theme = $applicationConfig[APPLICATION_MODULE]['view_themes']['current_theme'];

// include common config to get the error template
$commonConfig = include(APPLICATION_MODULE_PATH . '/config/common.module.config.php');
$errorTemplate = $commonConfig['view_manager']['exception_template'];

if ($theme == 'default') {
    $errorTemplateFile = $commonConfig['view_manager']['template_map'][$errorTemplate];
} else {
    $themePath = APPLICATION_MODULE_PATH . '/resources/themes/' . $theme;
    // include theme config to get the theme error template if defined
    $templateConfig = include($themePath . '/theme.config.php');
    $errorTemplate = $templateConfig['view_manager']['exception_template'];
    $errorTemplateFile = $templateConfig['view_manager']['template_map'][$errorTemplate];
}

include $errorTemplateFile;
