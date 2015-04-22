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

// define environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'live'));

// create applitacion-wide constants
require_once(__DIR__ . '/wh_application/library/application_constants.php');

$defaultFaviconFile = __DIR__ . '/wh_application/data/favicon/default.ico';
$moduleIcoFile = __DIR__ . '/wh_application/data/favicon/' . APPLICATION_MODULE . '.ico';

$favicon = file_exists($moduleIcoFile) ? $moduleIcoFile : $defaultFaviconFile;

header('Content-Type: image/x-icon');
echo file_get_contents($favicon);
exit(0);
