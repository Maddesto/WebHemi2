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
 * @package   WebHemi2_View_Helper
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\View\Helper;

use Zend\View\Helper\AbstractHelper;
use WebHemi2\Acl\Acl;

/**
 * View helper for ACL
 *
 * @category  WebHemi2
 * @package   WebHemi2_View_Helper
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class IsAllowed extends AbstractHelper
{
    /** @var Acl $authService */
    protected $aclService;

    /**
     * Check privilege
     *
     * @param string $resource
     * @param string $role
     * @return boolean
     */
    public function __invoke($resource, $role = null)
    {
        $acl = $this->getAclService();
        return $acl->isAllowed($resource, $role);
    }

    /**
     * Retrieve ACL service object
     *
     * @return Acl
     */
    public function getAclService()
    {
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
}
