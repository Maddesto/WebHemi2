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
 * @package    WebHemi_Theme
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Theme;

use Zend\Stdlib\PriorityQueue,
	Zend\ServiceManager\ServiceManager,
	Zend\ServiceManager\Exception,
	Zend\View\Resolver\AggregateResolver,
	Zend\View\Resolver\TemplateMapResolver,
	Zend\View\Resolver\TemplatePathStack,
	WebHemi\Theme\Adapter\ConfigurationAdapter;

/**
 * WebHemi theme manager
 *
 * @category   WebHemi
 * @package    WebHemi_Theme
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class ThemeManager
{
	/** @var array $opions */
	protected $options;
	/** @var PriorityQueue $themePath */
	protected $themePathList;
	/** @var PriorityQueue  $adapters */
	protected $adapterList;
	/** @var string  $currentTheme */
	protected $currentTheme   = null;
	/** @var ConfigurationAdapter  $currentAdapter */
	protected $currentAdapter = null;
	/** @var ServiceManager $serviceManager */
	protected $serviceManager;

	/**
	 * Instantiate a theme manager
	 *
	 * @param  array|Traversable $options
	 * @param ServiceManager     $serviceManager
	 *
	 * @return ThemeManager
	 * @throws Exception\InvalidArgumentException
	 */
	public static function factory($options, ServiceManager $serviceManager)
	{
		if ($options instanceof Traversable) {
			$options = ArrayUtils::iteratorToArray($options);
		}
		elseif (!is_array($options)) {
			throw new Exception\InvalidArgumentException(sprintf(
							'%s expects an array or Traversable object; received "%s"', __METHOD__, (is_object($options) ? get_class($options) : gettype($options))
			));
		}

		$themeManager = new static($options, $serviceManager);
		$themeManager->init();

		return $themeManager;
	}

	/**
	 * Class constructor
	 *
	 * @param array|Traversable $options
	 * @param ServiceManager    $serviceManager
	 */
	protected function __construct($options = array(), ServiceManager $serviceManager)
	{
		// set the options
		$this->options        = $options;
		// set the service manager
		$this->serviceManager = $serviceManager;
		// set the theme path list
		$this->themePathList  = new PriorityQueue();
		// set the theme selector adapter list
		$this->adapterList    = new PriorityQueue();
	}

	/**
	 * Initialize the theme and update the view resolver
	 *
	 * @return boolean
	 */
	public function init()
	{
		// fill up the theme path list
		if (isset($this->options['theme_paths'])) {
			$priority = count($this->options['theme_paths']);

			foreach ($this->options['theme_paths'] as $path) {
				$this->themePathList->insert($path, $priority--);
			}
		}
		// fill up the adapter list
		if (isset($this->options['adapters'])) {
			$priority = count($this->options['adapters']);

			foreach ($this->options['adapters'] as $adapterClass) {
				$adapter = new $adapterClass($this->serviceManager);
				$this->adapterList->insert($adapter, $priority--);
			}
		}

		// select the current theme
		if (!$this->selectCurrentTheme()) {
			return false;
		}

		// get the theme configuration
		$config = $this->getThemeConfig($this->currentTheme);
		// we're about to change the system-default view settings to custom
		$viewResolver  = $this->serviceManager->get('ViewResolver');
		$themeResolver = new AggregateResolver();

		if (isset($config['template_map'])) {
			$mapResolver = new TemplateMapResolver($config['template_map']);
			$themeResolver->attach($mapResolver);
		}

		if (isset($config['template_path_stack'])) {
			$pathResolver = new TemplatePathStack(
							$config['template_path_stack']
			);
			$defaultPathStack = $this->serviceManager->get('ViewTemplatePathStack');
			$pathResolver->setDefaultSuffix($defaultPathStack->getDefaultSuffix());
			$themeResolver->attach($pathResolver);
		}

		$viewResolver->attach($themeResolver, 100);
		return true;
	}

	/**
	 * Retrieve the current used theme
	 *
	 * @return string
	 */
	public function getTheme()
	{
		return $this->currentTheme;
	}

	/**
	 * Set the name of the new theme
	 *
	 * @param string $themeName
	 * @return boolean
	 */
	public function setTheme($themeName)
	{
		// if no valid adapter has been set
		if (!$this->currentAdapter) {
			return false;
		}

		return $this->currentAdapter->setTheme($this->filterThemeName($themeName));
	}

	/**
	 * Retrieve the theme configuration
	 *
	 * @param string $themeName     The name of the theme
	 * @return array
	 */
	public function getThemeConfig($themeName)
	{
		$themeName = $this->filterThemeName($themeName);

		// walk through paths
		for ($i = 0; $i < $this->themePathList->count(); $i++) {
			$themePath = $this->themePathList->extract() . $themeName . '/theme.config.php';

			// if found it and readable, return it
			if (file_exists($themePath) && is_readable($themePath)) {
				return include $themePath;
			}
		}

		return array();
	}

	/**
	 * Filters the theme name to be valid
	 *
	 * @param string $themeName        The name of the theme
	 * @return string
	 */
	protected function filterThemeName($themeName)
	{
		return str_replace(array('.', '/'), '', $themeName);
	}

	/**
	 * Select a valid theme
	 *
	 * @return string
	 */
	protected function selectCurrentTheme()
	{
		for ($i = 0; $i < $this->adapterList->count(); $i++) {
			$adapter = $this->adapterList->extract();
			$theme   = $adapter->getTheme();
			// if we found an adapter that provides a valid theme, we set them
			if ($theme) {
				$this->currentAdapter = $adapter;
				$this->currentTheme   = $theme;
				break;
			}
		}
		return $this->currentTheme;
	}

}