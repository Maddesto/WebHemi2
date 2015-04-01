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
 * @package   WebHemi2_Controller_Plugin
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Controller\Plugin;

use Zend\Http\Response;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\Plugin\Url;
use WebHemi2\Controller\AbstractController;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Controller plugin for redirecting
 *
 * @category  WebHemi2
 * @package   WebHemi2_Controller_Plugin
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Redirect extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface $serviceLocator
     */
    protected $serviceLocator;

    protected $event;
    protected $response;

    /**
     * Generates a URL based on a route
     *
     * @param  string $route RouteInterface name
     * @param  array $params Parameters to use in url generation, if any
     * @param  array $options RouteInterface-specific options to use in url generation, if any
     * @param  bool $reuseMatchedParams Whether to reuse matched parameters
     *
     * @return Response
     *
     * @throws Exception\DomainException if composed controller does not implement InjectApplicationEventInterface, or
     *         router cannot be found in controller event
     */
    public function toRoute($route = null, $params = [], $options = [], $reuseMatchedParams = false)
    {
        /** @var AbstractController $controller */
        $controller = $this->getController();
        if (!$controller || !method_exists($controller, 'plugin')) {
            throw new Exception\DomainException(
                'Redirect plugin requires a controller that defines the plugin() method'
            );
        }

        /** @var Url $urlPlugin */
        $urlPlugin = $controller->plugin('url');

        if (is_scalar($options)) {
            $url = $urlPlugin->fromRoute($route, $params, $options);
        } else {
            $url = $urlPlugin->fromRoute($route, $params, $options, $reuseMatchedParams);
        }

        return $this->toUrl($url);
    }

    /**
     * Redirect to the given URL
     *
     * @param  string $url
     *
     * @return Response
     */
    public function toUrl($url)
    {
        if (APPLICATION_MODULE_TYPE == APPLICATION_MODULE_TYPE_SUBDIR && APPLICATION_MODULE != WEBSITE_MODULE) {
            $url = '/' . APPLICATION_MODULE_URI . $url;
        }

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        return $response;
    }

    /**
     * Refresh to current route
     *
     * @return Response
     */
    public function refresh()
    {
        return $this->toRoute(null, [], [], true);
    }

    /**
     * Get the response
     *
     * @return Response
     *
     * @throws Exception\DomainException if unable to find response
     */
    protected function getResponse()
    {
        if ($this->response) {
            return $this->response;
        }

        $event = $this->getEvent();
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            throw new Exception\DomainException('Redirect plugin requires event compose a response');
        }
        $this->response = $response;
        return $this->response;
    }

    /**
     * Get the event
     *
     * @return MvcEvent
     *
     * @throws Exception\DomainException if unable to find event
     */
    protected function getEvent()
    {
        if ($this->event) {
            return $this->event;
        }

        $controller = $this->getController();
        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new Exception\DomainException(
                'Redirect plugin requires a controller that implements InjectApplicationEventInterface'
            );
        }

        $event = $controller->getEvent();
        if (!$event instanceof MvcEvent) {
            $params = $event->getParams();
            $event = new MvcEvent();
            $event->setParams($params);
        }
        $this->event = $event;

        return $this->event;
    }
}
