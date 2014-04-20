<?php

/**
 * WebHemi
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
 * @category   WebHemi
 * @package    WebHemi_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Event;

use Zend\Mvc\MvcEvent;

/**
 * Layout fixer event
 *
 * @category   WebHemi
 * @package    WebHemi_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
*/
class LayoutEvent
{
	/**
	 * Event handler. Fires upon dispatching the application
	 *
	 * @param MvcEvent $e
	 * @return void
	 */
	public static function preDispatch(MvcEvent $e)
	{
		$controller      = $e->getTarget();
		$controllerClass = get_class($controller);
		$moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
		$config          = $e->getApplication()->getServiceManager()->get('Configuration');

		if (isset($config['module_layouts'][$moduleNamespace])) {
			$controller->layout($config['module_layouts'][$moduleNamespace]);
		}
	}
}
