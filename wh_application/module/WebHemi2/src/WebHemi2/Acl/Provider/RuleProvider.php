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
 * @package    WebHemi2_Acl_Provider
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Acl\Provider;

/**
 * WebHemi2 Rule Container and Provider
 *
 * @category   WebHemi2
 * @package    WebHemi2_Acl_Provider
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class RuleProvider
{
    /** @var array $rules */
    protected $rules = array();

    /**
     * Class constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->rules = $config;
    }

    /**
     * Retrieve rules
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Add a new rule
     *
     * @param string $role
     * @param array $resources
     *
     * @return RuleProvider
     */
    public function addRule($role, array $resources)
    {
        $this->rules[] = array(
            'role' => $role,
            'resources' => $resources
        );

        return $this;
    }
}
