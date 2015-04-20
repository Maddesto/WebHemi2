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
 * @category  WebHemi2Test
 * @package   WebHemi2Test
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2Test;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\ModuleManager\ModuleManager;
use RuntimeException;

/**
 * WebHemi2Test
 *
 * Module bootstrap
 *
 * @category  WebHemi2Test
 * @package   WebHemi2Test
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Bootstrap
{
    /** @var  ServiceManager */
    protected static $serviceManager;

    /**
     * Init bootstrap
     *
     * @return void
     */
    public static function init()
    {
        error_reporting(E_ALL | E_STRICT);

        $zf2ModulePaths = [dirname(dirname(__DIR__))];
        if (($path = static::findParentPath('vendor'))) {
            $zf2ModulePaths[] = $path;
        }
        if (($path = static::findParentPath('module')) !== $zf2ModulePaths[0]) {
            $zf2ModulePaths[] = $path;
        }

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $config = [
            'module_listener_options' => [
                'module_paths' => $zf2ModulePaths,
            ],
            'modules' => [
                'WebHemi2'
            ]
        ];

        static::$serviceManager = new ServiceManager(new ServiceManagerConfig());
        static::$serviceManager->setService('ApplicationConfig', $config);

        /** @var ModuleManager $moduleManager */
        $moduleManager = static::$serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        $rootPath = dirname(static::findParentPath('module'));
        chdir($rootPath);
    }

    /**
     * Retrieve the service manager instance
     *
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    /**
     * Init the autoloader
     */
    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        // Composer autoloading
        if (file_exists($vendorPath . '/autoload.php')) {
            $loader = include $vendorPath . '/autoload.php';
        }

        if (class_exists('Zend\Loader\AutoloaderFactory')) {
            return;
        }

        $zf2Path = false;

        if (is_dir(__DIR__ . '/vendor/ZF2/library')) {
            $zf2Path = __DIR__ . '/vendor/ZF2/library';
        } elseif (getenv('ZF2_PATH')) {      // Support for ZF2_PATH environment variable or git submodule
            $zf2Path = getenv('ZF2_PATH');
        } elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
            $zf2Path = get_cfg_var('zf2_path');
        }

        if ($zf2Path) {
            if (isset($loader)) {
                $loader->add('Zend', $zf2Path);
                $loader->add('ZendXml', $zf2Path);
            } else {
                include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
                AutoloaderFactory::factory(
                    [
                        'Zend\Loader\StandardAutoloader' => [
                            'autoregister_zf' => true
                        ]
                    ]
                );
            }
        }

        if (!class_exists('Zend\Loader\AutoloaderFactory')) {
            throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
        }
    }

    /**
     * Find a specific folder in the parent path
     *
     * @param string $path The folder name or path
     *
     * @return bool|string
     */
    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}

Bootstrap::init();
