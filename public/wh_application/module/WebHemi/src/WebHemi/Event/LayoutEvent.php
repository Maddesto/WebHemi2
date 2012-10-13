<?php

namespace WebHemi\Event;

use Zend\Mvc\MvcEvent;

class LayoutEvent
{
    public function preDispatch(MvcEvent $e)
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
