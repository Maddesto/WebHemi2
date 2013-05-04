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
 * @package    WebHemi_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * WebHemi Website Controller
 *
 * @category   WebHemi
 * @package    WebHemi_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class WebsiteController extends AbstractActionController
{
	/**
     * Main page
     *
     * @return array
     */
	public function indexAction()
	{
		$this->userAuth()->hasIdentity();
		return array();
	}

	/**
     * View content
     *
     * @return array
     */
	public function viewAction()
	{
		$matches  = $this->getEvent()->getRouteMatch();
		$category = $matches->getParam('category', 'default');
		$id       = $matches->getParam('id', false);
		$format   = $matches->getParam('format', 'html');

		// emulate 404
		if ('nope' == $id) {
			$this->response->setStatusCode(404);
		}
		dump($category, '$category');
		dump($id, '$id');
		dump($format, '$format');
		return array();
	}

}
