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
 * @package    WebHemi_Acl
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Acl;

use Zend\ServiceManager\ServiceManager,
	Zend\Permissions\Acl\Acl as ZendAcl,
	Zend\Permissions\Acl\Exception,
	WebHemi\Acl\Provider\RoleProvider,
	WebHemi\Acl\Provider\ResourceProvider,
	WebHemi\Acl\Provider\RuleProvider,
	WebHemi\Acl\Assert\CleanIPAssertion;

/**
 * WebHemi Access Control
 *
 * @category   WebHemi
 * @package    WebHemi_Acl
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
		$this->options = $options;
		// set the service manager
		$this->serviceManager = $serviceManager;
		// set the ACL object
		$this->acl = new ZendAcl();
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

		$this->roleProvider     = new RoleProvider($this->options['roles'],         $this->serviceManager);
		$this->resourceProvider = new ResourceProvider($this->options['resources'], $this->serviceManager);
		$this->ruleProvider     = new RuleProvider($this->options['rules'],         $this->serviceManager);

		// build role tree in ACL
		$this->buildRoleTree($this->roleProvider->getRoles());

		// add the resources to the ACL
		foreach ($this->resourceProvider->getResources() as $resource) {
			$key = new \Zend\Permissions\Acl\Resource\GenericResource($resource->getResourceId());
			$this->acl->addResource($key, null);
		}

		// set rules
		$rules = $this->ruleProvider->getRules();
		if (isset($rules['allow'])) {
			foreach ($rules['allow'] as $resourceName => $roleName) {
				if ($this->acl->hasResource($resourceName) && $this->acl->hasRole($roleName)) {
					// allow the resources for the roles, except when the requesting IP is blacklisted.
					$this->acl->allow($roleName, $resourceName, null, new CleanIPAssertion());
				}
			}
		}

		if (isset($rules['deny'])) {
			foreach ($rules['deny'] as $resourceName => $roleName) {
				if ($this->acl->hasResource($resourceName) && $this->acl->hasRole($roleName)) {
					// deny the resources for the roles.
					$this->acl->deny($roleName, $resourceName);
				}
			}
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
	 * Restrieve the Zend ACL object
	 *
	 * @return Zend\Permissions\Acl\Acl
	 */
	public function getService()
	{
		return $this->acl;
	}

	/**
	 * Returns true if and only if the Role has access to the Resource
	 *
	 * @param  Resource\ResourceInterface|string    $resource
	 * @param  Role\RoleInterface|string            $role
	 * @return boolean
	 */
	public function isAllowed($resource, $role = null)
	{
		try {
			if (empty($role)) {
				// @TODO: If the webhemi\auth is complete, make this to use the current user's role
				$role = 'guest';
			}
			// If no role or no resourse we allow access
			if (!$this->acl->hasRole($role) || !$this->acl->hasResource($resource)) {
				return true;
			}
			return $this->acl->isAllowed($role, $resource);
		}
		// It is not necessary to terminate the script. Fair enough to return with a FALSE
		catch (Exception\InvalidArgumentException $e) {
			return false;
		}
	}
}
