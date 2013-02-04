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
 * @package    WebHemi_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
	Zend\ServiceManager\ServiceLocatorInterface,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	WebHemi\Acl\Role,
	WebHemi\Acl\Resource,
	WebHemi\Acl\Acl;

/**
 * Controller plugin for ACL
 *
 * @category   WebHemi
 * @package    WebHemi_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class IsAllowed extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /** @var Acl $aclService */
	protected $aclService;
	/** @var ServiceManager */
	protected $serviceManager;

	/**
	 * Return true if and only if the Role has access to the Resource.
	 * If a valid role is not coupled with a valid resource it will result FALSE.
	 * If the role or the resourse is not valid it will result TRUE.
	 *
	 * @param  Resource|string    $resource
	 * @param  Role|string        $role
	 * @return boolean
	 */
    public function __invoke($resource, $privilege = null)
    {
        $acl                = $this->getAclService();
		$controllerResource = 'Controller-' . ucfirst(strtolower($resource)) . '/*';
		$routeResource      = 'Route-' . $resource;

		return $acl->isAllowed($resource, $privilege)
				&& $acl->isAllowed($controllerResource, $privilege)
				&& $acl->isAllowed($routeResource, $privilege);
    }

	/**
	 * Retrieve ACL service object
	 *
	 * @return Acl
	 */
    public function getAclService()
    {
		if (!isset($this->aclService)) {
			$this->setAclService($this->serviceManager->getServiceLocator()->get('acl'));
		}
        return $this->aclService;
    }

	/**
	 * Set ACL service object
	 *
	 * @param Acl $aclService
	 * @return IsAllowed
	 */
    public function setAclService(Acl $aclService)
    {
        $this->aclService = $aclService;
        return $this;
    }

	/**
     * Set ServiceLocatorInterface instance
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return void
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Retrieve ServiceLocatorInterface instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
