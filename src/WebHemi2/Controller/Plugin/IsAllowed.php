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
 * @package    WebHemi2_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    WebHemi2\Acl\Role,
    WebHemi2\Acl\Resource,
    WebHemi2\Acl\Acl;

/**
 * Controller plugin for ACL
 *
 * @category   WebHemi2
 * @package    WebHemi2_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class IsAllowed extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /** @var Acl $aclService */
    protected $aclService;
    /** @var ServiceLocatorInterface */
    protected $serviceLocator;

    /**
     * Return true if and only if the Role has access to the Resource.
     * If a valid role is not coupled with a valid resource it will result FALSE.
     * If the role or the resourse is not valid it will result TRUE.
     *
     * @param  Resource|string    $resource
     * @param  Role|string        $role
     * @return boolean
     */
    public function __invoke($resource, $role = null)
    {
        return $this->getAclService()->isAllowed($resource, $role);
    }

    /**
     * Retrieve ACL service object
     *
     * @return Acl
     */
    public function getAclService()
    {
        if (!isset($this->aclService)) {
            $this->aclService = $this->getServiceLocator()->get('acl');
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
     * Retrieve ServiceLocatorInterface instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getController()->getServiceLocator();
    }

    /**
     * Set ServiceLocatorInterface instance
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return IsAllowed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
