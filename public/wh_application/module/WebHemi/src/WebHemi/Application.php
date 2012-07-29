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

/**
 * Initialize WebHemi Application, set paths, and bootstrap etc.
 *
 * @category   WebHemi
 * @package    WebHemi
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
final class Application
{
	/** The WebHemi's version */
	const WEBHEMI_VERSION = '2.0.1';

	/** The required minimal version of the Zend Framework */
	const MINIMUM_ZF_REQUIREMENT = '2.0';

	/** @var WebHemi\Application */
	public static $instance = null;
	/** @var array   A colletcion of configurations */
	private $configs;

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	private function __construct()
	{
		$this->setPaths();

		// Define current application module
		list($subdomain, $domain) = explode('.', $_SERVER['HTTP_HOST'], 2);
		// If the address is built only from 'domain.tld', then subdomain should be handled as 'www'
		if (strpos($domain, '.') === false) {
			$subdomain = 'www';
		}
		list(, $subdir) = explode('/', $_SERVER['REQUEST_URI'], 3);
		$modules = require_once APPLICATION_PATH . '/config/application.modules.config.php';
		$module = false;

		// we run through the available application-modules
		foreach ($modules as $moduleName => $moduleData) {
			// the default is the website-module
			if ($moduleData['path'] == 'www' && $subdomain == 'www') {
				$module = $moduleName;
				break;
			}
			elseif ($subdomain == 'www') {
				// subdirectory-based modules
				if (!empty($subdir)
						&& $moduleData['type'] == 'subdir'
						&& $moduleData['path'] == $subdir
				) {
					$module = $moduleName;
					break;
				}
			}
			else {
				// subdomain-based modules
				if ($moduleData['type'] == 'subdomain'
						&& $moduleData['path'] == $subdomain
				) {
					$module = $moduleName;
					break;
				}
			}
		}
		defined('APPLICATION_MODULE')
				|| define('APPLICATION_MODULE', ($module ? $module : 'Website'));

		$this->setConfig('Application', APPLICATION_PATH . '/config/application.config.php');
		$this->checkZendFrameworkVersion();
		$this->setAutoLoader();
	}

	/**
	 * Defines common paths and inserts vendor folder into include path
	 *
	 * @return void
	 */
	private function setPaths()
	{
		set_include_path(
				get_include_path() . PATH_SEPARATOR .
				APPLICATION_PATH . '/vendor' . PATH_SEPARATOR
		);

		// Define Zend Framework path
		if (!getenv('ZF2_PATH')) {
			foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
				if (is_dir($path . '/Zend')) {
					define('ZF2_PATH', $path . '/Zend');
					break;
				}
			}
		}
		else {
			define('ZF2_PATH', getenv('ZF2_PATH'));
		}
	}

	/**
	 * Checks whether the specific configuration exists
	 *
	 * @param string $name    The name of the config section
	 * @return array
	 */
	public function hasConfig($name)
	{
		return isset($this->configs[$name]);
	}

	/**
	 * Loads the given configuration
	 *
	 * @param string $name         The name of the config section
	 * @param string $filename     The path to the config file
	 * @param boolean $overwrite   Merge or overwrite
	 * @throws Exception
	 * @return void
	 */
	public function setConfig($name, $filename, $overwrite = false)
	{
		if (file_exists($filename) && is_readable($filename)) {
			$config = include $filename;

			// if the given path returns as an array
			if (is_array($config)) {
				// set or replace
				if ($overwrite || !isset($this->configs[$name])) {
					$this->configs[$name] = $config;
				}
				else {
					$this->configs[$name] = array_merge_recursive($this->configs[$name], $config);
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
	 * Gives back the specific configuration
	 *
	 * @param string $name    The name of the config section
	 * @return array
	 */
	public function getConfig($name)
	{
		return isset($this->configs[$name]) ? $this->configs[$name] : array();
	}

	/**
	 * Check Zend Framework version
	 *
	 * @throws Exception
	 * @return void
	 */
	private function checkZendFrameworkVersion()
	{
		// Check path
		if (!defined('ZF2_PATH') || !file_exists(ZF2_PATH . '/Version.php')) {
			throw new \Exception('<b>No Zend Framework found!</b>');
		}
		require_once ZF2_PATH . '/Version.php';

		// Check namespace and class and version
		if (!class_exists('Zend\Version')
				|| \Zend\Version::compareVersion(self::MINIMUM_ZF_REQUIREMENT) > 0) {
			throw new \Exception('<b>Your Zend Framework version is below required!'
					. ' (' . (\Zend\Version::VERSION) . ' vs. ' . self::MINIMUM_ZF_REQUIREMENT . ')</b>');
		}
	}

	/**
	 * Initialize the class autoloader
	 *
	 * @return void
	 */
	private function setAutoLoader()
	{
		require_once ZF2_PATH . '/Loader/AutoloaderFactory.php';

		\Zend\Loader\AutoloaderFactory::factory(array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					'WebHemi' => APPLICATION_PATH . '/module/WebHemi',
					'Zend' => ZF2_PATH,
				),
				'autoregister_zf' => true,
				'fallback_autoloader' => true,
			),
		));
	}

	/**
	 * Run WebHemi application
	 *
	 * @return void
	 */
	public static function run()
	{
		$instance = self::getInstance();

		// initialize the Zend Application
		$mvc = \Zend\Mvc\Application::init($instance->getConfig('Application'));
		// get the Zend\Http\PhpEnvironment\Response object
		$response = $mvc->run();
		// send the response to the client
		$response->send();
	}

	/**
	 * Instatiate WebHemi application
	 *
	 * @return WebHemi\Application
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
