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
 * @package   WebHemi2_Acl
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Acl;

use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * WebHemi2
 *
 * Resource
 *
 * @category  WebHemi2
 * @package   WebHemi2_Acl
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Resource implements ResourceInterface
{
    /** @var string $resourceId */
    protected $resourceId;

    /**
     * Class constructor
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
     *
     * @return Resource
     */
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;

        return $this;
    }
}
