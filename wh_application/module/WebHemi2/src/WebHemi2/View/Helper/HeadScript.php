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

use Zend\View\Exception;
use Zend\View\Helper\HeadScript as ZendHeadScript;

/**
 * View helper extension for the Zend View Helper HeadScript
 *
 * @category  WebHemi2
 * @package   WebHemi2_View_Helper
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class HeadScript extends ZendHeadScript
{
    /**
     * Overload method access
     *
     * @param  string $method Method to call
     * @param  array  $args   Arguments of method
     *
     * @throws Exception\BadMethodCallException if too few arguments or invalid method
     * @return HeadScript
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>set|(ap|pre)pend|offsetSet)(?P<mode>File|Script)$/', $method, $matches)) {
            if (1 > count($args)) {
                throw new Exception\BadMethodCallException(
                    sprintf(
                        'Method "%s" requires at least one argument',
                        $method
                    )
                );
            }

            $action = $matches['action'];
            $mode   = strtolower($matches['mode']);
            $type   = 'text/javascript';
            $attrs  = [];
            $index  = 0;

            if ('offsetSet' == $action) {
                $index = array_shift($args);
                if (1 > count($args)) {
                    throw new Exception\BadMethodCallException(
                        sprintf(
                            'Method "%s" requires at least two arguments, an index and source',
                            $method
                        )
                    );
                }
            }

            $content = $args[0];

            if (isset($args[1])) {
                $type = (string) $args[1];
            }
            if (isset($args[2])) {
                $attrs = (array) $args[2];
            }

            switch ($mode) {
                case 'script':
                    $item = $this->createData($type, $attrs, $content);
                    if ('offsetSet' == $action) {
                        $this->offsetSet($index, $item);
                    } else {
                        $this->$action($item);
                    }
                    break;
                case 'file':
                default:
                    if (!$this->isDuplicate($content)) {
                        $attrs['src'] = $content;
                        $item = $this->createData($type, $attrs);

                        // For local static file we should use the
                        if (strpos($item->attributes['src'], '/resources') === 0) {
                            list($protocol,) = explode('/', $_SERVER['SERVER_PROTOCOL']);
                            $item->attributes['src'] = strtolower($protocol)
                                . '://' . APPLICATION_STATIC_DOMAIN . $item->attributes['src'];
                        }

                        if ('offsetSet' == $action) {
                            $this->offsetSet($index, $item);
                        } else {
                            $this->$action($item);
                        }
                    }
                    break;
            }

            return $this;
        }

        return parent::__call($method, $args);
    }
}
