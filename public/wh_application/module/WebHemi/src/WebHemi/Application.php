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
	const WEBHEMI_VERSION        = '2.0.0.3';
	/** The required minimal version of the Zend Framework */
	const MINIMUM_ZF_REQUIREMENT = '2.0';
	/** The name of the admin module */
	const ADMIN_MODULE           = 'Admin';
	/** The name of the default module */
	const WEBSITE_MODULE         = 'Website';

	/** @staticvar WebHemi\Application */
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
		// add the vendor folder to the path
		set_include_path(
				get_include_path() . PATH_SEPARATOR .
				APPLICATION_PATH . '/vendor' . PATH_SEPARATOR
		);

		// define Zend Framework path
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
	 * Parse the URL and match against the config to determine the current module
	 *
	 * @param string $url
	 * @return string
	 */
	public static function getCurrentModuleFromUrl($url = '')
	{
		$modules   = require_once APPLICATION_PATH . '/config/application.modules.config.php';
		$module    = self::WEBSITE_MODULE;
		$subDomain = '';

		// if no URL is present, then the current URL will be used
		if (empty($url)) {
			$url  = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 's' : '') . '://';
			$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'];;
		}

		// parse the URL into
		$urlParts    = parse_url($url);

		// if the host is not an IP address, then we can check the subdomain-based module names too
		if (!preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $urlParts['host'])) {
			$domainParts = explode('.', $urlParts['host']);
			$tld         = array_pop($domainParts);
			$domain      = array_pop($domainParts) . '.' . $tld;
			$subDomain   = implode('.', $domainParts);
		}

		// If no subdomain present, then it should be handled as 'www'
		if (empty($subDomain)) {
			$subDomain = 'www';
		}

		// we ignore the first (actually an emtpy string) and last (the rest of the URL)
		list(, $subdir) = explode('/', $urlParts['path'], 3);

		// we run through the available application-modules
		foreach ($modules as $moduleName => $moduleData) {
			// subdirectory-based modules
			if ($subDomain == 'www') {
				if (!empty($subdir)
						&& $moduleData['type'] == 'subdir'
						&& $moduleData['path'] == $subdir
				) {
					$module = $moduleName;
					break;
				}
			}
			// subdomain-based modules
			else {
				if ($moduleData['type'] == 'subdomain'
						&& $moduleData['path'] == $subDomain
				) {
					$module = $moduleName;
					break;
				}
			}
		}
		return $module;
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
		if (!defined('ZF2_PATH') || !file_exists(ZF2_PATH . '/Version/Version.php')) {
			throw new \Exception('<b>No Zend Framework found!</b>');
		}
		require_once ZF2_PATH . '/Version/Version.php';

		// Check namespace and class and version
		if (!class_exists('Zend\Version\Version')
				|| \Zend\Version\Version::compareVersion(self::MINIMUM_ZF_REQUIREMENT) > 0) {
			throw new \Exception('<b>Your Zend Framework version is below required!'
					. ' (' . (\Zend\Version\Version::VERSION) . ' vs. ' . self::MINIMUM_ZF_REQUIREMENT . ')</b>');
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
				'autoregister_zf'     => true,
				'fallback_autoloader' => true,
				'namespaces'          => array(
					'WebHemi' => APPLICATION_PATH . '/module/WebHemi',
					'Zend'    => ZF2_PATH,
				),
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
		$mvc      = \Zend\Mvc\Application::init($instance->getConfig('Application'));
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
