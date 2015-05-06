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
 * @package   WebHemi2_Mvc
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Mvc;

use Zend\Mvc\Application as ZendApp;

/**
 * WebHemi2
 *
 * Application MVC shell
 *
 * @category  WebHemi2
 * @package   WebHemi2_Mvc
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Application extends ZendApp
{
    protected static $applicationConfig;

    /**
     * Static method for quick and easy initialization of the Application.
     *
     * @static
     * @param array $configuration Base configuration file
     * @return Application
     */
    public static function init($configuration = [])
    {
        self::$applicationConfig = !empty($configuration)
            ? $configuration
            : require __DIR__ . '/../../../../../config/application.config.php';

        // Run the application!
        return parent::init(self::$applicationConfig);
    }
}
