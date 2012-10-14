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
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\ServiceFactory;

use Zend\ServiceManager\FactoryInterface,
	Zend\ServiceManager\ServiceLocatorInterface,
	WebHemi\Form\FormService;

/**
 * WebHemi form service factory
 *
 * @category   WebHemi
 * @package    WebHemi_ServiceFactory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class FormServiceFactory implements FactoryInterface
{
    /**
	 * Factory method for WebHemi form service
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
	 * @return FormService
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
    {
		// @TODO: use it via 'service_manager => invokables'
		$service = new FormService($serviceLocator);
        return $service;
    }
}
