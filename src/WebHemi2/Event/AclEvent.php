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
 * @package    WebHemi2_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Event;

use Zend\Mvc\MvcEvent;

/**
 * ACL checker event
 *
 * @category   WebHemi2
 * @package    WebHemi2_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
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
        /* @var $acl \WebHemi2\Acl\Acl */
        $acl            = $serviceManager->get('acl');
        /* @var $auth \WebHemi2\Auth\Auth */
        $auth           = $serviceManager->get('auth');
        $routeMatch     = $e->getTarget()->getMvcEvent()->getRouteMatch();
        $controllerName = $routeMatch->getParam('controller');
        $actionName     = $routeMatch->getParam('action');

        // define the the resource
        $controllerArray = explode('\\', $controllerName);
        $controller = array_pop($controllerArray);
        $resource   = strtolower($controller . '/' . $actionName);
        // @TODO: get default_role option somehow insetad of 'guest'
        $role       = ($auth->hasIdentity()) ? $auth->getIdentity()->getRole() : 'guest';
        $allowed    = $acl->isAllowed($resource, $role);

        if (!$allowed) {
            // in admin module if there's no authenticated user, the user should be redirected to the login page
            if (APPLICATION_MODULE == ADMIN_MODULE
                    && 'login' != $actionName
                    && !$auth->hasIdentity()
            ) {
                $response = $e->getTarget()->getMvcEvent()->getResponse();
                $response->getHeaders()->addHeaderLine('Location', '/user/login');
                $response->setStatusCode(302);
                $response->send();
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