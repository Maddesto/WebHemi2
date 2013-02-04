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
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Event;

use Zend\Mvc\MvcEvent,
	WebHemi\Application;

/**
 * ACL checker event
 *
 * @category   WebHemi
 * @package    WebHemi_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
*/
class AclEvent
{
	/**
	 * Event handler. Fires upon route event
	 *
	 * @param MvcEvent $e
	 * @return void
	 */
    public static function onRoute(MvcEvent $e)
    {
        $serviceManager = $e->getTarget()->getServiceManager();
        $eventManager   = $e->getTarget()->getEventManager();
		$acl            = $serviceManager->get('acl');
		$auth           = $serviceManager->get('auth');
		$routeMatch     = $e->getTarget()->getMvcEvent()->getRouteMatch();
		$controllerName = $routeMatch->getParam('controller');
		$actionName     = $routeMatch->getParam('action');

		// define the possible resources
		$controller     = array_pop(explode('\\', $controllerName));
		$action         = preg_replace_callback(
				'/-([a-z])/',
				function($args)
				{
					return strtoupper($args[1]);
				},
				$actionName
		);

		// allow access to a full conntroller (be careful with it, wildcard for guests on your own risk)
		$wildCardControllerResource = 'Controller-' . $controller . '/*';
		// allow access to an action override the wildcard
		$controllerActionForcedResource   = '!Controller-' . $controller . '/' . $action;
		// allow access to an action
		$controllerActionResource   = 'Controller-' . $controller . '/' . $action;
		// allow access to an URL (be sure that the URL cannot be changed)
		$routeResource              = 'Route-' . $_SERVER['REQUEST_URI'];

		// isAllowed will return true for non-exist resources to not fail the expression for the valid parts
        $allowed = ($acl->isAllowed($wildCardControllerResource) || $acl->isAllowed($controllerActionForcedResource))
			&& $acl->isAllowed($controllerActionResource)
			&& $acl->isAllowed($routeResource);

		if (!$allowed) {
			// in admin module if there's no authenticated user, the user should be redirected to the login page
			if (APPLICATION_MODULE == Application::ADMIN_MODULE
					&& 'login' != $action
					&& !$auth->hasIdentity()
			) {
				$response = $e->getTarget()->getMvcEvent()->getResponse();
				$response->getHeaders()->addHeaderLine('Location', '/user/login');
				$response->setStatusCode(302);
			}
			// otherwise it's a 403 Frobidden error
			else {
				$e->setError('error-unauthorized-controller')
					->setParam('identity', $acl->getIdentity())
					->setParam('controller', $controllerName)
					->setParam('action', $actionName);

				$eventManager->trigger('dispatch.error', $e);
			}
        }
    }
}
