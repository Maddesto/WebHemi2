<?php

/**
 * WebHemi2
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
define('WEBHEMI_VERSION', '2.0.1.0');

define('ADMIN_MODULE', 'Admin');
define('WEBSITE_MODULE', 'Website');

define('AUTOLOGIN_COOKIE_PREFIX', 'atln');

define('APPLICATION_PATH', dirname(__DIR__));
define('APPLICATION_MODULE_TYPE_SUBDOMAIN', 'subdomain');
define('APPLICATION_MODULE_TYPE_SUBDIR', 'subdir');

$configFile = APPLICATION_PATH . '/config/application.config.php';
if (file_exists($configFile)) {
    /** @noinspection PhpIncludeInspection */
    $modules = include $configFile;
} else {
    $modules = [];
}

// For the unit test it is required to define these keys
if (php_sapi_name() === 'cli') {
    $_SERVER['SERVER_NAME'] = 'localhost';
    $_SERVER['HTTP_HOST'] = 'localhost';
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['QUERY_STRING'] = '';
    $_SERVER['REMOTE_ADDR'] = 'http://foo.org';
    $_SERVER['SERVER_PROTOCOL'] = 'HTTP';
}

define('APPLICATION_MODULE', call_user_func(
    function () {
        $domain = $_SERVER['SERVER_NAME'];

        $configFile = APPLICATION_PATH . '/config/application.config.php';
        if (file_exists($configFile)) {
            /** @noinspection PhpIncludeInspection */
            $modules = include($configFile);
        } else {
            $modules = [];
        }
        // set a default module
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
            '/^((\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.){3}(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/',
            $urlParts['host']
        )) {
            $domainParts = explode('.', $urlParts['host']);
            $tld = array_pop($domainParts);
            $domain = array_pop($domainParts) . '.' . $tld;
            $subDomain = implode('.', $domainParts);
        }


        // if no subdomain present, then it should be handled as 'www'
        if (empty($subDomain)) {
            $subDomain = 'www';
        }

        // additionally we store the domains as well
        define('APPLICATION_DOMAIN', $subDomain . '.' . $domain);
        define('APPLICATION_STATIC_DOMAIN', 'static.' . $domain);

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
    }, $modules)
);
define('APPLICATION_MODULE_TYPE', call_user_func(function ($moduleName, $modules) {
        return isset($modules[$moduleName])
            ? $modules[$moduleName]['type']
            : (WEBSITE_MODULE == $moduleName ? 'subdomain' : 'subdir');
    }, APPLICATION_MODULE, $modules)
);
define('APPLICATION_MODULE_URI', call_user_func(function ($moduleName, $modules) {
        return isset($modules[$moduleName])
            ? $modules[$moduleName]['path']
            : (WEBSITE_MODULE == $moduleName ? 'www' : '/');
    }, APPLICATION_MODULE, $modules)
);

// remove global variable
unset($modules);

// If no mcrypt extension present
defined('MCRYPT_RIJNDAEL_256') || define('MCRYPT_RIJNDAEL_256', 'rijndael-256');
defined('MCRYPT_MODE_CBC') || define('MCRYPT_MODE_CBC', 'cbc');
