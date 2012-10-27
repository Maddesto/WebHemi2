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
 * @package    WebHemi
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi;

use WebHemi\Application,
	Zend\Mvc\MvcEvent,
	Zend\Mvc\ModuleRouteListener,
	Zend\EventManager\EventInterface,
	Zend\ModuleManager\Feature\ConfigProviderInterface,
	Zend\ModuleManager\Feature\ServiceProviderInterface,
	Zend\ModuleManager\Feature\BootstrapListenerInterface,
	Zend\ModuleManager\Feature\AutoloaderProviderInterface;

/**
 * WebHemi module bootstrap
 *
 * @category   WebHemi
 * @package    WebHemi
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Module implements
	AutoloaderProviderInterface,
	BootstrapListenerInterface,
	ConfigProviderInterface,
	ServiceProviderInterface
{

	/**
	 * Listen to the bootstrap event
	 *
	 * @param EventInterface $e
	 */
	public function onBootstrap(EventInterface $e)
	{
		$serviceManager = $e->getApplication()->getServiceManager();
		$eventManager   = $e->getApplication()->getEventManager();

		// instantialize services
		$serviceManager->get('translator');
		$serviceManager->get('acl');

		if ($serviceManager->has('theme_manager')) {
			$serviceManager->get('theme_manager');
		}

		// attach events to the event manager
		$eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array('WebHemi\Event\ForbiddenEvent', 'prepareViewModel'), -5000);
		$eventManager->attach(MvcEvent::EVENT_ROUTE,          array('WebHemi\Event\AclEvent',       'onRoute'),          -1000);
		$eventManager->attach(MvcEvent::EVENT_DISPATCH,       array('WebHemi\Event\LayoutEvent',    'preDispatch'),       100);

		// link the event manager to the modoule route listener
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
	}

	/**
	 * Retrieves the Module Configuration
	 *
	 * @return array
	 */
	public function getConfig()
	{
		$hemiApplication = Application::getInstance();

		// for the first call, we set the Config
		if (!$hemiApplication->hasConfig('Module')) {
			// There's only tho physical modules (Admin and Website) the others are virtual modules and inherit from Website module
			$mainModule = APPLICATION_MODULE == Application::ADMIN_MODULE
					? Application::ADMIN_MODULE
					: Application::WEBSITE_MODULE;

			// load the general config
			$hemiApplication->setConfig('Module', __DIR__ . '/config/common.module.config.php');
			// load the main module config
			$hemiApplication->setConfig('Module', __DIR__ . '/config/' . $mainModule . '.module.config.php');
			// load the customizable configs
			$hemiApplication->setConfig('Module', __DIR__ . '/config/application.config.php', false, APPLICATION_MODULE);
		}
		return $hemiApplication->getConfig('Module');
	}

	/**
	 * Retrieve the Autoloader Configuration
	 *
	 * @return array
	 */
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	/**
	 * Retrieve the View Helper Configuration
	 *
	 * @return array
	 */
	public function getViewHelperConfig()
	{
		// already defined in the module config file
		return array();
	}

	/**
	 * Retrieve the Controller Plugin Configuration
	 *
	 * @return array
	 */
	public function getControllerPluginConfig()
	{
		// already defined in the module config file
		return array();
	}

	/**
	 * Retrieve the Service Configuration
	 *
	 * @return array
	 */
	public function getServiceConfig()
	{
		// already defined in the module config file
		return array();
	}
}
