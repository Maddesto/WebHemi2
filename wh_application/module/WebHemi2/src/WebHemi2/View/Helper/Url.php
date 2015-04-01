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

use Traversable;
use Zend\View\Exception;
use Zend\View\Helper\Url as ZendUrl;

/**
 * View helper extension for the Zend View Helper Url
 *
 * @category  WebHemi2
 * @package   WebHemi2_View_Helper
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Url extends ZendUrl
{
    /**
     * Generates a url given the name of a route. Additionally prepends with the module if the
     *
     * @see    Zend\Mvc\Router\RouteInterface::assemble()
     * @param  string               $name               Name of the route
     * @param  array                $params             Parameters for the link
     * @param  array|\Traversable    $options            Options for the route
     * @param  bool                 $reuseMatchedParams Whether to reuse matched parameters
     * @return string Url                         For the link href attribute
     * @throws Exception\RuntimeException         If no RouteStackInterface was provided
     * @throws Exception\RuntimeException         If no RouteMatch was provided
     * @throws Exception\RuntimeException         If RouteMatch didn't contain a matched route name
     * @throws Exception\InvalidArgumentException If the params object was not an array or \Traversable object
     */
    public function __invoke($name = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        $url = parent::__invoke($name, $params, $options, $reuseMatchedParams);

        if (APPLICATION_MODULE_TYPE == APPLICATION_MODULE_TYPE_SUBDIR && APPLICATION_MODULE !== WEBSITE_MODULE) {
            $url = '/' . APPLICATION_MODULE_URI . $url;
        }

        return $url;
    }
}
