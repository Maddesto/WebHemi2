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
 * @package    WebHemi_View_Strategy
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\View\Strategy;

use Zend\EventManager\EventInterface,
	Zend\EventManager\EventManagerInterface,
	Zend\EventManager\ListenerAggregateInterface,
	Zend\Http\Response as HttpResponse,
	Zend\Mvc\MvcEvent,
	Zend\Stdlib\ResponseInterface as Response,
	Zend\View\Model\ViewModel;

/**
 * WebHemi Forbidden view strategy
 *
 * @category   WebHemi
 * @package    WebHemi_View_Strategy
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class ForbiddenStrategy implements ListenerAggregateInterface
{
    /** @var string $template */
    protected $template = 'error/403';

    /** @var array $listenert */
    protected $listeners = array();

    /**
	 * Attach events
	 *
	 * @param \Zend\EventManager\EventManagerInterface $em
	 */
	public function attach(EventManagerInterface $em)
    {
        $this->listeners[] = $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'prepareViewModel'), -5000);
    }

	/*
	 * Detach evemts
	 *
	 * @param \Zend\EventManager\EventManagerInterface $em
	 */
    public function detach(EventManagerInterface $em)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($em->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

	/**
	 * Set template path
	 *
	 * @param string $template
	 */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

	/**
	 * Retrieve template path
	 *
	 * @return string
	 */
    public function getTemplate()
    {
        return $this->template;
    }

	/**
	 * Prepares the ACL error page
	 *
	 * @param \Zend\EventManager\EventInterface $e
	 * @return void
	 */
    public function prepareViewModel(EventInterface $e)
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
        $model->setTemplate($this->getTemplate());
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
