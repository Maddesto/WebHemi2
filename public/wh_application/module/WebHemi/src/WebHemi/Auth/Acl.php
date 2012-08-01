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
 * @package    WebHemi_Auth
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Auth;

use Zend\ServiceManager\ServiceManager,
	Zend\Permissions\Acl\Acl as ZendAcl,
	Zend\Permissions\Acl\Exception,
	WebHemi\Auth\Provider\RoleProvider,
	WebHemi\Auth\Provider\ResourceProvider,
	WebHemi\Auth\Provider\RuleProvider;

/**
 * WebHemi Access Control
 *
 * @category   WebHemi
 * @package    WebHemi_Auth
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Acl
{
	/** @var array $opions */
	protected $options;
	/** @var Zend\ServiceManager\ServiceManager $serviceManager */
	protected $serviceManager;
	/** @var Zend\Permissions\Acl\Acl $acl */
    protected $acl;
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
	 * @param  array|Traversable $options
	 * @param ServiceManager     $serviceManager
	 *
	 * @return WebHemi\ServiceManager\ThemeManager
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

		$acl = new static($options, $serviceManager);
		$acl->init();

		return $acl;
	}

    /**
	 * Constructor
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
		// set the ACL object
		$this->acl            = new ZendAcl();
	}

	/**
	 * Initialize the access control service
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
			$key = new \Zend\Permissions\Acl\Resource\GenericResource($resource->getResourceId());
			$this->acl->addResource($key, null);
		}

		// set rules
		$defaults = array(
			'role'       => null,
			'resources'  => null,
			'privileges' => null,
			'assertion'  => null,
		);
		foreach ($this->ruleProvider->getRules() as $rules) {
			// make sure every index is set
			$rules = array_values(array_merge($defaults, $rules));
			// export the values from the array;
			list($roles, $resources, $privileges, $assertion) = $rules;
			// allow the resources for the roles
			$this->acl->allow($roles, $resources, $privileges, $assertion);
		}
	}

	/**
	 * Add roles to the ACL
	 *
	 * @param string|array $roles
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
				throw new Exception\InvalidArgumentException(sprintf(
					'%s expects an array of Role objects; received "%s"', __METHOD__, (is_object($role) ? get_class($role) : gettype($role))
				));
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
            }
			else {
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
	 * Returns true if and only if the Role has access to the Resource
	 *
	 * @param  Role\RoleInterface|string            $role
     * @param  Resource\ResourceInterface|string    $resource
	 * @return boolean
	 */
	public function isAllowed($role, $resource = null)
    {
        try {
			// if no resource is given, but the role seems to be a valid resource then we expect it is resource indeed
			// and try to retrieve the role from the user session
			if (empty($resource)
				&& ($role instanceof Zend\Permissions\Acl\Resource\ResourceInterface
					|| $this->acl->hasResource($role)
			)) {
				$resource = $role;
				// @TODO: megcsinalni majd, ha lesz auth, hogy az aktualis felhasznalo role-ja keruljon be
				$role = 'guest';
			}

            return $this->acl->isAllowed($role, $resource);
        }
		// it is not necessary to throw exception here. Fair enough to return with a FALSE
		catch (Exception\InvalidArgumentException $e) {
            return false;
        }
    }
}
