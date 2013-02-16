<?php

/**
 * WebHemi
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
 * @category   WebHemi
 * @package    WebHemi_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Event;

use WebHemi\Application,
	Zend\Mvc\MvcEvent,
	Zend\View\Model\ViewModel;

/**
 * Frobidden error handler event
 *
 * @category   WebHemi
 * @package    WebHemi_Event
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
*/
class ErrorEvent
{
    /** @staticvar string $template */
    static $template = array(
		403 => 'error/403',
		404 => 'error/404'
	);

	/**
	 * Prepares the ACL error page
	 *
	 * @param MvcEvent $e
	 * @return void
	 */
    public function preDispatch(MvcEvent $e)
    {
        // Do nothing if the result is a response object
        $result = $e->getResult();
        if ($result instanceof Response) {
            return;
        }

        // Common view variables
        $viewVariables = array(
           'error'      => $e->getParam('error'),
           'identity'   => $e->getParam('identity'),
        );

        $error = $e->getError();
        switch($error)
        {
            case 'error-unauthorized-controller':
            case 'error-unauthorized-route':
				self::get403($e);
                break;
            default:
				self::get404($e);
        }


    }

	/**
	 * Prepares the 403 error page
	 *
	 * @param MvcEvent $e
	 * @return void
	 */
	protected static function get403(MvcEvent $e)
	{
		$error = $e->getError();
        switch($error)
        {
            case 'error-unauthorized-controller':
                $viewVariables['controller'] = $e->getParam('controller');
                $viewVariables['action']     = $e->getParam('action');
                break;
            case 'error-unauthorized-route':
                $viewVariables['route'] = $e->getParam('route');
                break;
        }

		// add our error page to the view model
		$layout = $e->getViewModel();
		$layout->title = '403 Forbidden';

		if (Application::ADMIN_MODULE == APPLICATION_MODULE
				&& $e->getApplication()->getServiceManager()->get('auth')->hasIdentity()
		) {
			$headerBlock = new ViewModel();
			$headerBlock->setTemplate('block/AdminHeaderBlock');

			$menuBlock = new ViewModel();
			$menuBlock->setTemplate('block/AdminMenuBlock');

			$footerBlock = new ViewModel();
			$footerBlock->setTemplate('block/AdminFooterBlock');

			$layout->addChild($headerBlock, 'HeaderBlock')
				->addChild($menuBlock, 'MenuBlock')
				->addChild($footerBlock, 'FooterBlock');
		}

        $model = new ViewModel($viewVariables);
        $model->setTemplate(self::$template[403]);
		$model->error = $error;
        $layout->addChild($model);

        $response = $e->getResponse();

		// if no response object present, we create one
        if (!$response) {
            $response = new HttpResponse();
            $e->setResponse($response);
        }
        $response->setStatusCode(403);
	}

	/**
	 * Prepares the 404 error page
	 *
	 * @param MvcEvent $e
	 * @return void
	 */
	protected static function get404(MvcEvent $e)
	{
		// add our error page to the view model
		$layout = $e->getViewModel();
		$layout->title = '404 Not Found';

		if (Application::ADMIN_MODULE == APPLICATION_MODULE
				&& $e->getApplication()->getServiceManager()->get('auth')->hasIdentity()
		) {
			$headerBlock = new ViewModel();
			$headerBlock->setTemplate('block/AdminHeaderBlock');

			$menuBlock = new ViewModel();
			$menuBlock->setTemplate('block/AdminMenuBlock');

			$footerBlock = new ViewModel();
			$footerBlock->setTemplate('block/AdminFooterBlock');

			$layout->addChild($headerBlock, 'HeaderBlock')
				->addChild($menuBlock, 'MenuBlock')
				->addChild($footerBlock, 'FooterBlock');
		}

        $model = new ViewModel();
        $model->setTemplate(self::$template[404]);
		$model->error = $e->getError();
        $layout->addChild($model);

        $response = $e->getResponse();

		// if no response object present, we create one
        if (!$response) {
            $response = new HttpResponse();
            $e->setResponse($response);
        }
        $response->setStatusCode(404);
	}
}
