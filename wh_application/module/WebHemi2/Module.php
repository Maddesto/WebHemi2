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
 * @package   WebHemi2
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2;

use WebHemi2\View\Helper\Url;
use Zend\Console\Console;
use Zend\Mvc;
use Zend\ServiceManager;
use Zend\EventManager;
use Zend\View\HelperPluginManager;
use Zend\ModuleManager\Feature;

/**
 * WebHemi2
 *
 * module bootstrap
 *
 * @category  WebHemi2
 * @package   WebHemi2
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\BootstrapListenerInterface,
    Feature\ConfigProviderInterface
{
    /** @var array $configs A collection of configurations. */
    private static $configs;

    /**
     * Listen to the bootstrap event
     *
     * @param EventManager\EventInterface $e
     *
     * @return void
     */
    public function onBootstrap(EventManager\EventInterface $e)
    {
        /** @var Mvc\MvcEvent $e */
        /** @var Mvc\Application $application */
        $application = $e->getApplication();
        /** @var ServiceManager\ServiceManager $serviceManager */
        $serviceManager = $application->getServiceManager();
        /** @var EventManager\EventManager $eventManager */
        $eventManager   = $application->getEventManager();
        /** @var HelperPluginManager $viewHelperManager */
        $viewHelperManager = $serviceManager->get('ViewHelperManager');

        // instantiate services
        $serviceManager->get('translator');
        $serviceManager->get('acl');

        if ($serviceManager->has('theme_manager')) {
            $serviceManager->get('theme_manager');
        }

        // update view helper url
        $viewHelperManager->setFactory('url', function ($sm) use ($serviceManager) {
                $helper = new Url;
                $router = Console::isConsole() ? 'HttpRouter' : 'Router';
                /** @var Mvc\Router\RouteStackInterface $routerStack */
                $routerStack = $serviceManager->get($router);
                $helper->setRouter($routerStack);

                $match = $serviceManager->get('application')
                    ->getMvcEvent()
                    ->getRouteMatch();

                if ($match instanceof Mvc\Router\RouteMatch) {
                    $helper->setRouteMatch($match);
                }

                return $helper;
            });

        // attach events to the event manager
        $eventManager->attach(Mvc\MvcEvent::EVENT_DISPATCH_ERROR, ['WebHemi2\Event\ErrorEvent',  'preDispatch'], -500);
        $eventManager->attach(Mvc\MvcEvent::EVENT_ROUTE, ['WebHemi2\Event\AclEvent',    'onRoute'], -100);
        $eventManager->attach(Mvc\MvcEvent::EVENT_DISPATCH, ['WebHemi2\Event\LayoutEvent', 'preDispatch'], 10);

        // link the event manager to the modoule route listener
        $moduleRouteListener = new Mvc\ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * Retrieves the Module Configuration
     *
     * @return array
     */
    public function getConfig()
    {
        include_once __DIR__ . '/resources/application_constants.php';

        // for the first call, we set the Config
        if (!isset($configs)) {
            // There's only tho physical modules (Admin and Website) the others are virtual modules and inherit
            // from Website module
            $mainModule = APPLICATION_MODULE == ADMIN_MODULE
                    ? ADMIN_MODULE
                    : WEBSITE_MODULE;

            // load the general config
            $this->setConfig(__DIR__ . '/config/common.module.config.php');
            // load the main module config
            $this->setConfig(__DIR__ . '/config/' . $mainModule . '.module.config.php');
            // load the customizable configs
            $this->setConfig(__DIR__ . '/config/application.config.php', false, APPLICATION_MODULE);
        }

        return self::$configs;
    }

    /**
     * Load the given configuration.
     *
     * @param string $filename   The path to the config file.
     * @param boolean $overwrite Merge or overwrite.
     * @param string $segment    Includes only a segment of the config.
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function setConfig($filename, $overwrite = false, $segment = null)
    {
        if (file_exists($filename) && is_readable($filename)) {
            /** @noinspection PhpIncludeInspection */
            $config = include $filename;

            // if segment is given
            if (!empty($segment)) {
                if (isset($config[$segment])) {
                    $config = $config[$segment];
                } else {
                    throw new \Exception('Unknown segment (' . $segment . ') in the config.');
                }
            }

            // if the given path returns as an array
            if (is_array($config)) {
                // set or replace
                if ($overwrite || !isset(self::$configs)) {
                    self::$configs = $config;
                } else {
                    self::$configs = $this->mergeConfig(self::$configs, $config);
                }
            } else {
                throw new \Exception('The given path does not contain any configurations');
            }
        } else {
            throw new \Exception('File not exists or not readable: ' . $filename);
        }
    }

    /**
     * Merge config arrays in the correct way.
     * This rewrites the given key->value pairs and does not make key->array(value1, value2) like the
     * `array_merge_recursive` does.
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function mergeConfig()
    {
        if (func_num_args() < 2) {
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' needs two or more array arguments');
        }
        $arrays = func_get_args();
        $merged = [];

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
                        $merged[$key] = $this->mergeConfig($merged[$key], $value);
                    } else {
                        $merged[$key] = $value;
                    }
                } else {
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
    }
}
