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

chdir(__DIR__);
define('DOC_ROOT', dirname(__DIR__) . '/..');
echo 'Document root: ' . DOC_ROOT . PHP_EOL;
// Create symlink for common static folder
echo DOC_ROOT . '/wh_application/module/WebHemi2/resources/common/static/' . PHP_EOL;
symlink(DOC_ROOT . '/wh_application/module/WebHemi2/resources/common/static/', DOC_ROOT . '/resources/common/');
// Create symlink for default theme
symlink(DOC_ROOT . '/wh_application/module/WebHemi2/resources/default/static/', DOC_ROOT . '/resources/theme/default/');

if ($handle = opendir(DOC_ROOT . '/wh_application/module/WebHemi2/resources/themes/')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo "$entry\n";
        }
    }
    closedir($handle);
}