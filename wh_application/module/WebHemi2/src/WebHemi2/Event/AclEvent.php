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
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Event;

use WebHemi2\Model\Acl as AclModel;
use Zend\Mvc\MvcEvent;

/**
 * ACL checker event
 *
 * @category   WebHemi2
 * @package    WebHemi2_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class AclEvent
{
    /**
     * Event handler. Fires upon route event
     *
     * @param MvcEvent $e
     *
     * @return void
     */
    public static function onRoute(MvcEvent $e)
    {
        /** @var \Zend\ServiceManager\ServiceManager $serviceManager */
        $serviceManager = $e->getTarget()->getServiceManager();
        /** @var \Zend\EventManager\EventManager $eventManager */
        $eventManager = $e->getTarget()->getEventManager();
        /** @var $acl \WebHemi2\Acl\Acl */
        $acl = $serviceManager->get('acl');
        /** @var $auth \WebHemi2\Auth\Auth */
        $auth = $serviceManager->get('auth');
        /** @var \Zend\Mvc\Router\Http\RouteMatch $routeMatch */
        $routeMatch = $e->getTarget()->getMvcEvent()->getRouteMatch();

        $actionName = $routeMatch->getParam('action');
        $actionName = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $actionName));

        $controllerName = $routeMatch->getParam('controller');
        $controllerArray = explode('\\', $controllerName);
        $controller = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', array_pop($controllerArray)));

        // define the the resource
        $resource = $controller . ':' . $actionName;
        $role = ($auth->hasIdentity()) ? $auth->getIdentity()->getRole() : AclModel::ROLE_GUEST;
        $allowed = $acl->isAllowed($resource, $role);

        if (!$allowed) {
            // in admin module if there's no authenticated user, the user should be redirected to the login page
            if (APPLICATION_MODULE == ADMIN_MODULE
                && 'login' != $actionName
                && !$auth->hasIdentity()
            ) {
                $url = '/login/';
                if (APPLICATION_MODULE_TYPE == APPLICATION_MODULE_TYPE_SUBDIR && APPLICATION_MODULE != WEBSITE_MODULE) {
                    $url = '/' . APPLICATION_MODULE_URI . $url;
                }

                /** @var \Zend\Http\PhpEnvironment\Response $response */
                $response = $e->getTarget()->getMvcEvent()->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->send();
            } else {
                // otherwise it's a 403 Frobidden error
                $e->setError('error-unauthorized-controller')
                    ->setParam('identity', $acl->getIdentity())
                    ->setParam('controller', $routeMatch->getParam('controller'))
                    ->setParam('action', $routeMatch->getParam('action'));

                $eventManager->trigger('dispatch.error', $e);
            }
        }
    }
}
