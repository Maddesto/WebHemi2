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

use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * WebHemi Resource
 *
 * @category   WebHemi
 * @package    WebHemi_Acl
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Resource implements ResourceInterface
{
	/** @var string $resourceId */
	protected $resourceId;

	/**
	 * Class contructor
	 * If no resourceId is given, then it can be set with $role->setResourceId($resourceId);
	 *
	 * @param string $resourceId
	 */
	public function __construct($resourceId = null)
	{
		$this->resourceId = $resourceId;
	}

	/**
	 * Retrieve resourceId
	 *
	 * @return string
	 */
	public function getResourceId()
	{
		return $this->resourceId;
	}

	/**
	 * Set resourceId
	 *
	 * @param string $resourceId
	 * @return Resource
	 */
	public function setResourceId($resourceId)
	{
		$this->resourceId = $resourceId;
		return $this;
	}

}
