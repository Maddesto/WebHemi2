<?php

namespace WebHemi\Event;

use Zend\Mvc\MvcEvent;

class AclEvent
{
    public static function onRoute(MvcEvent $e)
    {
        $serviceManager = $e->getTarget()->getServiceManager();
        $eventManager   = $e->getTarget()->getEventManager();
		$acl            = $serviceManager->get('acl');
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

		// allows access to a full conntroller (be careful with it, wildcard for guests on your own risk)
		$wildCardControllerResource = 'Controller-' . $controller . '/*';
		// allows access to an action
		$controllerActionResource   = 'Controller-' . $controller . '/' . $action;
		// allows access to an URL (be sure that the URL cannot be changed)
		$routeResource              = 'Route-' . $_SERVER['REQUEST_URI'];

		// isAllowed will return true for non-exist resources to not make the expression being false
        $allowed = $acl->isAllowed($wildCardControllerResource)
			&& $acl->isAllowed($controllerActionResource)
			&& $acl->isAllowed($routeResource);

		if (!$allowed) {
            $e->setError('error-unauthorized-controller')
                ->setParam('identity', $acl->getIdentity())
                ->setParam('controller', $controllerName)
                ->setParam('action', $actionName);

            $eventManager->trigger('dispatch.error', $e);
        }
    }
}