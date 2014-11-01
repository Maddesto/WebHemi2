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
 * @package    WebHemi2_Acl
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Acl;

use Traversable;
use WebHemi2\Acl\Role as AclRole;
use WebHemi2\Acl\Resource as AclResource;
use WebHemi2\Acl\Provider\RoleProvider;
use WebHemi2\Acl\Provider\ResourceProvider;
use WebHemi2\Acl\Provider\RuleProvider;
use WebHemi2\Acl\Assert\CleanIPAssertion;
use WebHemi2\Model\Acl as AclModel;
use WebHemi2\Model\User as UserModel;
use WebHemi2\Model\Table\Acl as AclTable;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceManager;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Exception;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Authentication\AuthenticationService;
use Zend\Stdlib\ArrayUtils;

/**
 * WebHemi2 Access Control
 *
 * @category   WebHemi2
 * @package    WebHemi2_Acl
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Acl
{
    /** Define default role */
    const DEFAULT_ROLE = AclModel::ROLE_GUEST;

    /** @var array $options */
    protected $options;
    /** @var ServiceManager $serviceManager */
    protected $serviceManager;
    /** @var ZendAcl $acl */
    protected $acl;
    /** @var AuthenticationService $auth */
    protected $auth;
    /** @var string $template */
    protected $template = 'error/403';
    /** @var RoleProvider $roleProvider */
    protected $roleProvider;
    /** @var ResourceProvider $resourceProvider */
    protected $resourceProvider;
    /** @var RuleProvider $ruleProvider */
    protected $ruleProvider;

    /**
     * Instantiate the Access Control
     *
     * @param array|Traversable $options
     * @param ServiceManager $serviceManager
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return Acl
     */
    public static function factory($options, ServiceManager $serviceManager)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s expects an array or Traversable object; received "%s"',
                    __METHOD__,
                    (is_object($options) ? get_class($options) : gettype($options))
                )
            );
        }

        $acl = new static($options, $serviceManager);
        $acl->init();

        return $acl;
    }

    /**
     * Class constructor
     *
     * @param array|Traversable $options
     * @param ServiceManager $serviceManager
     */
    protected function __construct($options, ServiceManager $serviceManager)
    {
        // set the options
        $this->options = $options;
        // set the service manager
        $this->serviceManager = $serviceManager;
        // set the ACL object
        $this->acl = new ZendAcl();
        // deny all by default
        $this->acl->deny();
        // set the UserAuth service
        $this->auth = $this->serviceManager->get('auth');

        // set the template if given (otherwise the default will be used)
        if (isset($this->options['template'])) {
            $this->template = $this->options['template'];
        }
    }

    /**
     * Initialize the access control service
     *
     * @return Acl
     */
    public function init()
    {
        /** @var Adapter $adapter */
        $adapter = $this->serviceManager->get('database');
        $aclTable = new AclTable($adapter);

        $this->roleProvider = new RoleProvider($aclTable->getRoles(), $this->serviceManager);
        $this->resourceProvider = new ResourceProvider($aclTable->getResources(), $this->serviceManager);
        $this->ruleProvider = new RuleProvider($aclTable->getAclList(), $this->serviceManager);

        // add roles tree to the ACL
        foreach ($this->roleProvider->getRoles() as $role) {
            /** @var AclRole $role */
            $this->acl->addRole($role);
        }

        // add the resources to the ACL
        foreach ($this->resourceProvider->getResources() as $resource) {
            /** @var AclResource $resource */
            $key = new GenericResource($resource->getResourceId());
            $this->acl->addResource($key, null);
        }

        // prepare assertion object
        $assert = new CleanIPAssertion($this->serviceManager);

        // setup the acl: explicit allow resource to role, don't waste time with role tree
        foreach ($this->ruleProvider->getRules() as $resourceName => $roles) {
            if ($this->acl->hasResource($resourceName)) {
                foreach($roles as $roleName) {
                    if ($this->acl->hasRole($roleName)) {
                        // allow the resources for the roles, except when the requesting IP is blacklisted.
                        $this->acl->allow($roleName, $resourceName, null, $assert);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve the template path
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Retrieve the Zend ACL object
     *
     * @return ZendAcl
     */
    public function getService()
    {
        return $this->acl;
    }

    /**
     * Return true if and only if the Role has access to the Resource.
     * If a valid role is not coupled with a valid resource it will result FALSE.
     * If the role or the resourse is not valid it will result TRUE.
     *
     * @param  Resource|string $resource
     * @param  Role|string $role
     *
     * @return boolean
     */
    public function isAllowed($resource, $role = null)
    {
        try {
            if (empty($role)) {
                $role = $this->hasIdentity()
                    ? $this->getIdentity()->getRole()
                    : static::DEFAULT_ROLE;
            }

            if (!$this->acl->hasResource($resource)) {
                return false;
            }

            list(, $action) = explode(':', $resource);

            // allow access for login page, invalid role or non-forced resources
            if ('logout' == $action
                || 'login' == $action
                || !$this->acl->hasRole($role)
            ) {
                return true;
            }

            return $this->acl->isAllowed($role, $resource);
        } catch (Exception\InvalidArgumentException $e) {
            // It is not necessary to terminate the whole script running. Fair enough to return with a FALSE.
            return false;
        }
    }

    /**
     * Check whether the user is authenticated
     *
     * @return boolean
     */
    public function hasIdentity()
    {
        if ($this->auth instanceof AuthenticationService) {
            return $this->auth->hasIdentity();
        }

        return false;
    }

    /**
     * Retrive the User entity
     *
     * @return UserModel
     */
    public function getIdentity()
    {
        if ($this->hasIdentity()) {
            return $this->auth->getIdentity();
        }

        return null;
    }

    /**
     * Check whether a resource exists
     *
     * @param  Resource|string $resource
     *
     * @return boolean
     */
    public function hasResource($resource)
    {
        return $this->acl->hasResource($resource);
    }

    /**
     * Check whether a role exists
     *
     * @param  Role|string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        return $this->acl->hasRole($role);
    }
}
