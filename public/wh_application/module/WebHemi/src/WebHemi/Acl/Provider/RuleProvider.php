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
 * @package    WebHemi_Acl_Provider
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Acl\Provider;

/**
 * WebHemi Rule Container and Provider
 *
 * @category   WebHemi
 * @package    WebHemi_Acl_Provider
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class RuleProvider
{
	/** @var array $rules */
	protected $rules = array();

	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array())
	{
		$this->rules = $config;
	}

	/**
	 * Retrieve all rules
	 *
	 * @return array
	 */
	public function getRules()
	{
		return $this->rules;
	}

}
