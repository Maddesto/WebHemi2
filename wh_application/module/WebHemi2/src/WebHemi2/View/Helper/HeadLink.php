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
 * @package    WebHemi2_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\View\Helper;

use Zend\View\Exception;
use Zend\View\Helper\HeadLink as ZendHeadLink;

/**
 * View helper extension for the Zend View Helper HeadLink
 *
 * @category   WebHemi2
 * @package    WebHemi2_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class HeadLink extends ZendHeadLink
{
    /**
     * Overload method access
     *
     * @param  string $method Method to call
     * @param  array  $args   Arguments of method
     *
     * @throws Exception\BadMethodCallException
     *
     * @return void
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>set|(ap|pre)pend|offsetSet)(?P<type>Stylesheet|Alternate|Prev|Next)$/', $method, $matches)) {
            $argc   = count($args);
            $action = $matches['action'];
            $type   = $matches['type'];
            $index  = null;

            if ('offsetSet' == $action) {
                if (0 < $argc) {
                    $index = array_shift($args);
                    --$argc;
                }
            }

            if (1 > $argc) {
                throw new Exception\BadMethodCallException(sprintf(
                        '%s requires at least one argument',
                        $method
                    ));
            }

            if (is_array($args[0])) {
                $item = $this->createData($args[0]);
            } else {
                $dataMethod = 'createData' . $type;
                $item       = $this->$dataMethod($args);
            }

            if ($item) {
                if (strpos($item->href, '/resources') === 0) {
                    list($protocol,) = explode('/', $_SERVER['SERVER_PROTOCOL']);
                    $item->href = strtolower($protocol) . '://' . APPLICATION_STATIC_DOMAIN . $item->href;
                }

                if ('offsetSet' == $action) {
                    $this->offsetSet($index, $item);
                } else {
                    $this->$action($item);
                }
            }

            return $this;
        }

        return parent::__call($method, $args);
    }
}
