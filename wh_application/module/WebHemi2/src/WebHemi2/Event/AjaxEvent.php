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

use Zend\Json\Json;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use WebHemi2\Form\AbstractForm;

/**
 * Ajax response event
 *
 * @category  WebHemi2
 * @package   WebHemi2_Event
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class AjaxEvent
{
    /**
     * Prepares the Ajax response
     *
     * @param MvcEvent $event
     * @return void
     */
    public static function preRender(MvcEvent $event)
    {
        /** @var Request $request */
        $request = $event->getRequest();
        /** @var Response $response */
        $response = $event->getResponse();

        // if the request was an AJAX request, then we provide an AJAX response
        if ($request->isXmlHttpRequest()) {
            /** @var ViewModel $viewModel */
            $viewModel = $event->getResult();
            // stop listening for more events
            $event->stopPropagation(true);
            $responseData = [
                'success' => true,
                'error' => [],
                'data' => []
            ];

            if ($viewModel instanceof ViewModel) {
                // collect all the view variables
                $viewVariables = $viewModel->getVariables();

                foreach ($viewVariables as $key => $value) {
                    // if the value is a Form, we search for error messages
                    if ($value instanceof AbstractForm) {
                        $messages = $value->getMessages();

                        if (!empty($messages)) {
                            $responseData['error'][$key] = $messages;
                        }
                    } else {
                        $responseData['data'][$key] = $value;
                    }
                }
            }

            // set the error data, if the response status code is not 200
            switch ($response->getStatusCode()) {
                case $response::STATUS_CODE_403:
                case $response::STATUS_CODE_404:
                case $response::STATUS_CODE_500:
                    $responseData['error'][$response->getStatusCode()] = $response->getReasonPhrase();
                    break;
            }

            if (!empty($responseData['error'])) {
                $responseData['success'] = false;
            }

            $response->getHeaders()->addHeaderLine('Content-type: application/json');
            $response->setContent(Json::encode($responseData));
        }
    }
}
