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
	Zend\Mvc\MvcEvent as Event,
	Zend\Mvc\ModuleRouteListener,
	Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
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
    ConfigProviderInterface,
    ServiceProviderInterface,
	AutoloaderProviderInterface
{
	/**
	 * Runs automatically upon bootstrapping
	 *
	 * @param \Zend\Mvc\MvcEvent $e
	 */
    public function onBootstrap(Event $e)
    {
		$serviceManager = $e->getApplication()->getServiceManager();
        $eventManager	  = $e->getApplication()->getEventManager();

		// Instantialize services
		if ($serviceManager->has('translator')) {
			$serviceManager->get('translator');
		}

		if ($serviceManager->has('theme_manager')) {
			$serviceManager->get('theme_manager');
		}

		// Link the event manager to the modoule route listener
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
		if (!$hemiApplication->hasConfig('Module')) {
			$hemiApplication->setConfig('Module', __DIR__ . '/config/module.config.php');
			$hemiApplication->setConfig('Module', __DIR__ . '/config/' . APPLICATION_MODULE . '.module.config.php');
		}
		return $hemiApplication->getConfig('Module');
    }

	/**
	 * Retrieves the Autoloader Configuration
	 *
	 * @return array
	 */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	/**
     * Retrieves the Service Configuration
	 *
     * @return array
     */
    public function getServiceConfig()
    {
        // already defined in the module-specific config file
		return array();
    }
}
