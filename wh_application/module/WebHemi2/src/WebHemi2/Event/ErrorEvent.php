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
 * @package   WebHemi2_Event
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Event;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;

/**
 * Forbidden error handler event
 *
 * @category  WebHemi2
 * @package   WebHemi2_Event
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class ErrorEvent
{
    /**
     * Prepares the ACL error page
     *
     * @param MvcEvent $event
     * @return void
     */
    public static function postDispatchError(MvcEvent $event)
    {
        // Do nothing if the result is a response object
        $result = $event->getResult();
        if ($result instanceof Response) {
            return;
        }

        $error = $event->getError();
        switch ($error) {
            case 'error-unauthorized-controller':
            case 'error-unauthorized-route':
                $layout = self::get403($event);
                $code = 403;
                break;

            default:
                $code = 404;
                $layout = self::get404($event);
        }

        $event->setViewModel($layout);

        /** @var Response $response */
        $response = $event->getResponse();

        // if no response object present, we create one
        if (!$response) {
            $response = new \HttpResponse();
            $event->setResponse($response);
        }
        $response->setStatusCode($code);
    }

    /**
     * Prepares the 403 error page
     *
     * @param MvcEvent $event
     * @return ViewModel
     */
    protected static function get403(MvcEvent $event)
    {
        // Common view variables
        $viewVariables = [
            'error' => $event->getParam('error'),
            'identity' => $event->getParam('identity'),
        ];

        $error = $event->getError();
        switch ($error) {
            case 'error-unauthorized-controller':
                $viewVariables['controller'] = $event->getParam('controller');
                $viewVariables['action'] = $event->getParam('action');
                break;
            case 'error-unauthorized-route':
                $viewVariables['route'] = $event->getParam('route');
                break;
        }

        // add our error page to the view model
        $layout = $event->getViewModel();
        $layout->setVariable('title', '403 Forbidden');

        $model = new ViewModel($viewVariables);
        $model->setTemplate('error/403');
        $model->setVariable('error', $error);
        $layout->addChild($model, 'content');

        return $layout;
    }

    /**
     * Prepares the 404 error page
     *
     * @param MvcEvent $event
     * @return ViewModel
     */
    protected static function get404(MvcEvent $event)
    {
        // add our error page to the view model
        $layout = $event->getViewModel();
        $layout->setVariable('title', '404 Not Found');

        $model = new ViewModel();
        $model->setTemplate('error/404');
        $model->setVariable('reason', $event->getError());
        $layout->addChild($model);

        return $layout;
    }
}
