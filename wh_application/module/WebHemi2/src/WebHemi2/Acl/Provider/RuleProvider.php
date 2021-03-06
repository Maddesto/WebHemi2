<?php

/**
 * WebHemi2
 *
 * PHP version 5.4
 *
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
 * @category  WebHemi2
 * @package   WebHemi2_Acl_Provider
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Acl\Provider;

use WebHemi2\Model\Acl;

/**
 * WebHemi2
 *
 * Rule Container and Provider
 *
 * @category  WebHemi2
 * @package   WebHemi2_Acl_Provider
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class RuleProvider
{
    /** @var array $rules */
    protected $rules = [];

    /**
     * Class constructor
     *
     * @param Acl[] $config
     */
    public function __construct($config = [])
    {
        foreach ($config as $resource => $rule) {
            foreach (ACL::$availableRoles as $role) {
                // if the resource is enable for the role
                if ($rule->getRule($role)) {
                    $this->addRule($resource, $role);
                }
            }
        }
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
     * @param string $resource
     * @param string $role
     *
     * @return RuleProvider
     */
    public function addRule($resource, $role)
    {
        if (!isset($this->rules[$resource])) {
            $this->rules[$resource] = [];
        }
        $this->rules[$resource][] = $role;

        return $this;
    }
}
