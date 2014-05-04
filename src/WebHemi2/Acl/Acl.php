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
use Zend\ServiceManager\ServiceManager;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Exception;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Authentication\AuthenticationService;
use WebHemi2\Acl\Provider\RoleProvider;
use WebHemi2\Acl\Provider\ResourceProvider;
use WebHemi2\Acl\Provider\RuleProvider;
use WebHemi2\Acl\Role;
use WebHemi2\Acl\Resource;
use WebHemi2\Acl\Assert\CleanIPAssertion;
use WebHemi2\Model\User as UserModel;

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
    /**
     * @var array $options
     */
    protected $options;
    /**
     * @var ServiceManager $serviceManager
     */
    protected $serviceManager;
    /**
     * @var ZendAcl $acl
     */
    protected $acl;
    /**
     * @var AuthenticationService $auth
     */
    protected $auth;
    /**
     * @var string $template
     */
    protected $template = 'error/403';
    /**
     * @var RoleProvider $roleProvider
     */
    protected $roleProvider;
    /**
     * @var ResourceProvider $resourceProvider
     */
    protected $resourceProvider;
    /**
     * @var RuleProvider $ruleProvider
     */
    protected $ruleProvider;

    /**
     * Instantiate the Access Control
     *
     * @param array|Traversable $options
     * @param ServiceManager    $serviceManager
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
     * @param ServiceManager    $serviceManager
     */
    protected function __construct($options, ServiceManager $serviceManager)
    {
        // set the options
        $this->options = $options;
        // set the service manager
        $this->serviceManager = $serviceManager;
        // set the ACL object
        $this->acl = new ZendAcl();
        // set the UserAuth service
        $this->auth = $this->serviceManager->get('auth');
    }

    /**
     * Initialize the access control service
     *
     * @return Acl
     */
    public function init()
    {
        // set the template if given (otherwise the default will be used)
        if (isset($this->options['template'])) {
            $this->template = $this->options['template'];
        }

        $this->roleProvider     = new RoleProvider($this->options['roles'], $this->serviceManager);
        $this->resourceProvider = new ResourceProvider($this->options['resources'], $this->serviceManager);
        $this->ruleProvider     = new RuleProvider($this->options['rules'], $this->serviceManager);

        // build role tree in ACL
        $this->buildRoleTree($this->roleProvider->getRoles());

        // add the resources to the ACL
        foreach ($this->resourceProvider->getResources() as $resource) {
            $key = new GenericResource($resource->getResourceId());
            $this->acl->addResource($key, null);
        }

        // set rules
        $rules = $this->ruleProvider->getRules();
        foreach ($rules as $resourceName => $roleName) {
            if ($this->acl->hasResource($resourceName) && $this->acl->hasRole($roleName)) {
                // allow the resources for the roles, except when the requesting IP is blacklisted.
                $this->acl->allow($roleName, $resourceName, null, new CleanIPAssertion($this->serviceManager));
            }
        }

        return $this;
    }

    /**
     * Add roles to the ACL
     *
     * @param string|array $roles
     *
     * @throws Exception\InvalidArgumentException
     */
    protected function buildRoleTree($roles)
    {
        if (!is_array($roles)) {
            $roles = array($roles);
        }

        foreach ($roles as $role) {
            // if the role is a troll :)
            if (!$role instanceof Role) {
                throw new Exception\InvalidArgumentException(
                    sprintf(
                        '%s expects an array of Role objects; received "%s"',
                        __METHOD__,
                        (is_object($role) ? get_class($role) : gettype($role))
                    )
                );
            }

            // if the role has already been set
            if ($this->acl->hasRole($role)) {
                continue;
            }

            $parentRole = $role->getParentRole();

            // if there is parent, we recursively add it
            if ($parentRole !== null) {
                $this->buildRoleTree($parentRole);
                $this->acl->addRole($role, $parentRole);
            } else {
                $this->acl->addRole($role);
            }
        }
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
     * @param  Role|string     $role
     *
     * @return boolean
     */
    public function isAllowed($resource, $role = null)
    {
        try {
            if (empty($role)) {
                $role = $this->hasIdentity()
                        ? $this->getIdentity()->getRole()
                        : $this->options['default_role'];
            }

            if (strpos($resource, '/') !== false) {
                list($controller, $action) = explode('/', $resource);
            } else {
                $controller = $resource;
                $action     = '*';
            }
            $controller = ucfirst(strtolower($controller));

            // allow access to a full conntroller (be careful with it, wildcard for guests on your own risk)
            $wildCardControllerResource = 'Controller-' . $controller . '/*';
            // allow access to an action
            $controllerActionResource   = 'Controller-' . $controller . '/' . $action;
            // allow access to an action override the wildcard
            $controllerActionForcedResource   = $action == '*' ? false : '!Controller-' . $controller . '/' . $action;
            // allow access to an URL (be sure that the URL cannot be changed)
            $routeResource              = 'Route-' . $_SERVER['REQUEST_URI'];

            // allow access for login page, invalid role or non-forced resources
            if ('logout' == $action
                || 'login' == $action
                || !$this->acl->hasRole($role)
            ) {
                return true;
            }

            $allowed = (
                    (
                        !$this->acl->hasResource($wildCardControllerResource)
                        || $this->acl->isAllowed($role, $wildCardControllerResource)
                    )
                    || (
                        $this->acl->hasResource($controllerActionForcedResource)
                        || $this->acl->isAllowed($role, $controllerActionForcedResource)
                    )
                )
                && (
                    !$this->acl->hasResource($controllerActionResource)
                    || $this->acl->isAllowed($role, $controllerActionResource)
                )
                && (
                    !$this->acl->hasResource($routeResource)
                    || $this->acl->isAllowed($role, $routeResource)
                );

            return $allowed;
        } catch (Exception\InvalidArgumentException $e) {
            // It is not necessary to terminate the script. Fair enough to return with a FALSE
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
