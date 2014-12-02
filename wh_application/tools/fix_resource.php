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
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

define('SCRIPT_VERSION', 'v.1.0');

chdir(__DIR__);
define('DOC_ROOT', realpath(dirname(__DIR__) . '/..'));

echo 'WebHemi2 resource builder (' . SCRIPT_VERSION . ')' . PHP_EOL . PHP_EOL;

// Create symlink for common static folder
echo 'Create symbolic link for common resources: ';
if (!file_exists(DOC_ROOT . '/resources/common')) {
    if (symlink(DOC_ROOT . '/wh_application/module/WebHemi2/resources/common/static', DOC_ROOT . '/resources/common')) {
        echo 'Done' . PHP_EOL;
    }
} else {
    echo 'already exists...' . PHP_EOL;
}

// Create symlink for default theme
echo 'Create symbolic link for default theme: ';
if (!file_exists(DOC_ROOT . '/resources/theme/default')) {
    if (symlink(DOC_ROOT . '/wh_application/module/WebHemi2/resources/default/static', DOC_ROOT . '/resources/theme/default')) {
        echo 'Done' . PHP_EOL;
    }
} else {
    echo 'already exists...' . PHP_EOL;
}

// Create symlink for all additional themes
echo 'Create symbolic link for additional themes: ' . PHP_EOL;
if ($handle = opendir(DOC_ROOT . '/wh_application/module/WebHemi2/resources/themes/')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != '.' && $entry != '..') {
            if (
                is_dir(DOC_ROOT . '/wh_application/module/WebHemi2/resources/themes/' . $entry)
                && file_exists(DOC_ROOT . '/wh_application/module/WebHemi2/resources/themes/' . $entry . '/static')
            ) {
                echo "\t-> " . $entry . ': ';

                if (!file_exists(DOC_ROOT . '/resources/theme/' . $entry)) {
                    if (symlink(DOC_ROOT . '/wh_application/module/WebHemi2/resources/themes/' . $entry . '/static', DOC_ROOT . '/resources/theme/' . $entry)) {
                        echo 'Done' . PHP_EOL;
                    }
                } else {
                    echo 'already exists...' . PHP_EOL;
                }

            }
        }
    }
    closedir($handle);
}

// exit program normally
exit(0);
