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
 * @package    WebHemi2
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2;

require_once __DIR__ . '/resources/application_constants.php';

use Zend\Mvc\MvcEvent,
    Zend\Mvc\ModuleRouteListener,
    Zend\EventManager\EventInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface;

/**
 * WebHemi2 module bootstrap
 *
 * @category   WebHemi2
 * @package    WebHemi2
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    /** @staticvar array $configs   A colletcion of configurations. */
    private static $configs;

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
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array('WebHemi2\Event\ErrorEvent',  'preDispatch'), -500);
        $eventManager->attach(MvcEvent::EVENT_ROUTE,          array('WebHemi2\Event\AclEvent',    'onRoute'),     -100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH,       array('WebHemi2\Event\LayoutEvent', 'preDispatch'),   10);

        // link the event manager to the modoule route listener
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * Check whether the specific configuration exists.
     *
     * @param string $name    The name of the config section.
     * @return bool
     */
    public static function hasConfig($name)
    {
        return isset(self::$configs[$name]);
    }

    /**
     * Retrieves the Module Configuration
     *
     * @return array
     */
    public function getConfig()
    {
        // for the first call, we set the Config
        if (!isset(self::$configs['Module'])) {
            // There's only tho physical modules (Admin and Website) the others are virtual modules and inherit
            // from Website module
            $mainModule = APPLICATION_MODULE == ADMIN_MODULE
                    ? ADMIN_MODULE
                    : WEBSITE_MODULE;

            // load the general config
            $this->setConfig('Module', __DIR__ . '/config/common.module.config.php');
            // load the main module config
            $this->setConfig('Module', __DIR__ . '/config/' . $mainModule . '.module.config.php');
            // load the customizable configs
            $this->setConfig('Module', __DIR__ . '/config/application.config.php', false, APPLICATION_MODULE);
        }

        return self::$configs['Module'];
    }

    /**
     * Load the given configuration.
     *
     * @param string $name         The name of the config section.
     * @param string $filename     The path to the config file.
     * @param boolean $overwrite   Merge or overwrite.
     * @param string $segment      Includes only a segment of the config.
     * @throws Exception
     * @return void
     */
    protected function setConfig($name, $filename, $overwrite = false, $segment = null)
    {
        if (file_exists($filename) && is_readable($filename)) {
            $config = include $filename;

            // if segment is given
            if (!empty($segment) ) {
                if (isset($config[$segment])) {
                    $config = $config[$segment];
                }
                else {
                    throw new \Exception('Unknown segment (' . $segment . ') in the config.');
                }
            }

            // if the given path returns as an array
            if (is_array($config)) {
                // set or replace
                if ($overwrite || !isset(self::$configs[$name])) {
                    self::$configs[$name] = $config;
                }
                else {
                    self::$configs[$name] = $this->mergeConfig(self::$configs[$name], $config);
                }
            }
            else {
                throw new \Exception('The given path does not contain any configurations');
            }
        }
        else {
            throw new \Exception('File not exists or not readable: ' . $filename);
        }
    }

    /**
     * Merge config arrays in the correct way.
     * This rewrites the given key->value pairs and does not make key->array(value1, value2) like the
     * `array_merge_recursive` does.
     *
     * @param array _   Two or more arrays to be merged.
     *
     * @return array
     */
    protected function mergeConfig()
    {
        dump();
        if (func_num_args() < 2) {
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' needs two or more array arguments');
        }
        $arrays = func_get_args();
        $merged = array();

        while ($arrays) {
            $array = array_shift($arrays);
            if (!is_array($array)) {
                throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' encountered a non array argument');
            }

            if (!$array) {
                continue;
            }

            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                        $merged[$key] = self::mergeConfig($merged[$key], $value);
                    }
                    else {
                        $merged[$key] = $value;
                    }
                }
                else {
                    $merged[] = $value;
                }
            }
        }
        return $merged;
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
