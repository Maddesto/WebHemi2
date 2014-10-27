<?php
/**
 * WebHemi Autoloader solution
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
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
require_once __DIR__ . '/library/dump.php';

if (class_exists('Zend\Loader\AutoloaderFactory')) {
    return;
}



$zf2Path = false;
$paths = explode(PATH_SEPARATOR, get_include_path());

// add the module directory to the path
array_unshift($paths, __DIR__ . '/module');

// add every third-party library to the path
if ($handle = opendir(__DIR__ . '/vendor')) {
    while (false !== ($entry = readdir($handle))) {
        if (
            strpos($entry, '.') !== 0
            && is_dir(__DIR__ . '/vendor/' . $entry)
            && file_exists(__DIR__ . '/vendor/' . $entry . '/library')
        ) {
            $path = __DIR__ . '/vendor/' . $entry . '/library';

            // Looking for Zend Framework 2
            if (file_exists($path . '/Zend/Version/Version.php')) {
                $zf2Path = $path;
            }

            array_unshift($paths, $path);
        }
    }
    closedir($handle);
}

set_include_path(implode(PATH_SEPARATOR, $paths));

if (!$zf2Path) {
    if (getenv('ZF2_PATH')) {      // Support for ZF2_PATH environment variable or git submodule
        $zf2Path = getenv('ZF2_PATH');
    } elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
        $zf2Path = get_cfg_var('zf2_path');
    }
}

if ($zf2Path) {
    include 'Zend/Loader/AutoloaderFactory.php';
    Zend\Loader\AutoloaderFactory::factory(array(
        'Zend\Loader\StandardAutoloader' => array(
            'autoregister_zf' => true
        )
    ));
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException('Unable to load ZF2. Define a ZF2_PATH environment variable.');
}
