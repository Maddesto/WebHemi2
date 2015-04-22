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
 * @package   WebHemi2_Event
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Event;

use Zend\Mvc\MvcEvent;

/**
 * Layout fixer event
 *
 * @category  WebHemi2
 * @package   WebHemi2_Event
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class LayoutEvent
{
    /**
     * Event handler. Fires upon dispatching the application
     *
     * @param MvcEvent $event
     *
     * @return void
     */
    public static function preDispatch(MvcEvent $event)
    {
        $controller = $event->getTarget();
        $controllerClass = get_class($controller);
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $config = $event->getApplication()->getServiceManager()->get('Configuration');

        if (isset($config['module_layouts'][$moduleNamespace])) {
            $controller->layout($config['module_layouts'][$moduleNamespace]);
        }
    }
}
