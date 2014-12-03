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

define('WEBHEMI_VERSION', '2.0.1.0');

define('APPLICATION_PATH', dirname(__DIR__));

define('ADMIN_MODULE', 'Admin');
define('WEBSITE_MODULE', 'Website');

define('APPLICATION_MODULE_TYPE_SUBDOMAIN', 'subdomain');
define('APPLICATION_MODULE_TYPE_SUBDIR', 'subdir');

define('APPLICATION_MODULE', call_user_func(function() {
        $modules = include APPLICATION_PATH . '/config/application.config.php';
        $module = WEBSITE_MODULE;
        $subDomain = '';

        // if no URL is present, then the current URL will be used
        if (empty($url)) {
            $url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 's' : '') . '://';
            $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'];
        }

        // parse the URL into
        $urlParts = parse_url($url);

        // if the host is not an IP address, then we can check the subdomain-based module names too
        if (!preg_match(
            '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/',
            $urlParts['host']
        )
        ) {
            $domainParts = explode('.', $urlParts['host']);
            $tld = array_pop($domainParts);
            $domain = array_pop($domainParts) . '.' . $tld;
            $subDomain = implode('.', $domainParts);
        }

        // if no subdomain present, then it should be handled as 'www'
        if (empty($subDomain)) {
            $subDomain = 'www';
        }

        // we ignore the first (actually an emtpy string) and last (the rest of the URL)
        list(, $subdir) = explode('/', $urlParts['path'], 3);

        // we run through the available application-modules
        foreach ($modules as $moduleName => $moduleData) {
            // subdirectory-based modules
            if ($subDomain == 'www') {
                if (!empty($subdir)
                    && $moduleData['type'] == APPLICATION_MODULE_TYPE_SUBDIR
                    && $moduleData['path'] == $subdir
                ) {
                    $module = $moduleName;
                    break;
                }
            } else {
                // subdomain-based modules
                if ($moduleData['type'] == APPLICATION_MODULE_TYPE_SUBDOMAIN
                    && $moduleData['path'] == $subDomain
                ) {
                    $module = $moduleName;
                    break;
                }
            }
        }

        return $module;
    })
);
define('APPLICATION_MODULE_TYPE', call_user_func(function($moduleName) {
        $modules = include APPLICATION_PATH . '/config/application.config.php';

        return isset($modules[$moduleName]) ? $modules[$moduleName]['type'] : 'subdir';
    }, APPLICATION_MODULE)
);
define('APPLICATION_MODULE_URI', call_user_func(function($moduleName) {
        $modules = include APPLICATION_PATH . '/config/application.config.php';

        return isset($modules[$moduleName]) ? $modules[$moduleName]['path'] : '/';
    }, APPLICATION_MODULE)
);

// If no mcrypt extension present
defined('MCRYPT_RIJNDAEL_256') || define('MCRYPT_RIJNDAEL_256', 'rijndael-256');
defined('MCRYPT_MODE_CBC') || define('MCRYPT_MODE_CBC', 'cbc');