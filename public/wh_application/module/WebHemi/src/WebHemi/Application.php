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
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi;

/**
 * Initialize WebHemi Application, set paths, and bootstrap etc.
 *
 * @category   WebHemi
 * @package    WebHemi
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
final class Application
{
	/** The WebHemi's version */
	const WEBHEMI_VERSION        = '2.0.0.17';
	/** The required minimal version of the Zend Framework */
	const MINIMUM_ZF_REQUIREMENT = '2.1.5';
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
	 * Define common paths and inserts vendor folder into include path
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
		$modules   = require_once WEBHEMI_PATH . '/config/application.config.php';
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
	 * Check whether the specific configuration exists
	 *
	 * @param string $name    The name of the config section
	 * @return array
	 */
	public function hasConfig($name)
	{
		return isset($this->configs[$name]);
	}

	/**
	 * Load the given configuration
	 *
	 * @param string $name         The name of the config section
	 * @param string $filename     The path to the config file
	 * @param boolean $overwrite   Merge or overwrite
	 * @param string $segment      Includes only a segment of the config
	 * @throws Exception
	 * @return void
	 */
	public function setConfig($name, $filename, $overwrite = false, $segment = null)
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
				if ($overwrite || !isset($this->configs[$name])) {
					$this->configs[$name] = $config;
				}
				else {
					$this->configs[$name] = $this->mergeConfig($this->configs[$name], $config);
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
	 * Merge config arrays in the correct way
	 * This rewrites the given key->value pairs and does not make key->array(value1, value2) like the
	 * `array_merge_recursive` does
	 *
	 * @return array
	 */
	private function mergeConfig()
	{
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
//						$merged[$key] = call_user_func(array(__CLASS__, __METHOD__), $merged[$key], $value);
						$merged[$key] = $this->mergeConfig($merged[$key], $value);
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
	 * retrievethe specific configuration
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
		$factoryConfig = array();
		// there is a ZF2 autoload map file, we use it prior of the standard autoload
		if (file_exists(ZF2_PATH . '/autoload_classmap.php')) {
			require_once ZF2_PATH . '/Loader/ClassMapAutoloader.php';
			$factoryConfig['Zend\Loader\ClassMapAutoloader'] = array(
                ZF2_PATH . '/autoload_classmap.php',
            );
		}

		$factoryConfig['Zend\Loader\StandardAutoloader'] = array(
			'autoregister_zf'     => true,
			'fallback_autoloader' => true,
			'namespaces'          => array(
				'WebHemi' => WEBHEMI_PATH,
				'Zend'    => ZF2_PATH,
			),
		);

		require_once ZF2_PATH . '/Loader/AutoloaderFactory.php';
		\Zend\Loader\AutoloaderFactory::factory($factoryConfig);
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

	/**
	 * Dump data
	 *
	 * @param  mixed   $var   The variable to dump
     * @param  string  $label OPTIONAL Label to prepend to output
     * @param  bool    $echo  OPTIONAL Echo output if true
     * @return string
	 */
	function varDump($data, $label = null, $echo = true)
	{
		require_once ZF2_PATH . '/Debug/Debug.php';
		$output = \Zend\Debug\Debug::dump($data, null, false);

		$output = highlight_string('<' . '?php ' . strip_tags($output), true);
		$output = '<strong>' . $label . '</strong><br />' . str_replace('&lt;?php', '', $output) . '<br />';

		if ($echo) {
			echo $output;
		}
		return $output;
	}

}