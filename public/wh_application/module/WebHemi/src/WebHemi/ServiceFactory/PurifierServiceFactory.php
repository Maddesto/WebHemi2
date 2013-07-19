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
 * @package    WebHemi_ServiceFactory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\ServiceFactory;

use Zend\ServiceManager\FactoryInterface,
	Zend\ServiceManager\ServiceLocatorInterface,
	\HTMLPurifier,
	\HTMLPurifier_Config;

/**
 * HTML Purifier service factory
 *
 * @category   WebHemi
 * @package    WebHemi_ServiceFactory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class PurifierServiceFactory implements FactoryInterface
{
	/**
	 * Factory method for WebHemi theme manager service
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
	 * @return HTMLPurifier
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$config = $serviceLocator->get('Configuration');
		$options = $config['purifier']['config'];

		$purifierConfig = HTMLPurifier_Config::createDefault();
		foreach ($options as $key => $value) {
			$purifierConfig->set($key, $value);
		}

		$purifier = new HTMLPurifier($purifierConfig);
		return $purifier;
	}
}