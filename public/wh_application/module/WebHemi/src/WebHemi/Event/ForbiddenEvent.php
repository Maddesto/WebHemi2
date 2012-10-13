<?php

namespace WebHemi\Event;

use Zend\Mvc\MvcEvent,
	Zend\View\Model\ViewModel;

class ForbiddenEvent
{
    /** @staticvar string $template */
    static $template = 'error/403';

	/**
	 * Prepares the ACL error page
	 *
	 * @param MvcEvent $e
	 * @return void
	 */
    public function prepareViewModel(MvcEvent $e)
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
                $viewVariables['controller'] = $e->getParam('controller');
                $viewVariables['action']     = $e->getParam('action');
                break;
            case 'error-unauthorized-route':
                $viewVariables['route'] = $e->getParam('route');
                break;
            default:
                return;
        }

		// add our error page to the view model
        $model = new ViewModel($viewVariables);
        $model->setTemplate(self::$template);
        $e->getViewModel()->addChild($model);

        $response = $e->getResponse();

		// if no response object present, we create one
        if (!$response) {
            $response = new HttpResponse();
            $e->setResponse($response);
        }
        $response->setStatusCode(403);
    }
}
