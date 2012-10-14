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
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
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
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class WebsiteController extends AbstractActionController
{
	/**
     * Default action
     *
     * @return array
     */
	public function indexAction()
	{
		return array();
	}

	// @TODO: implement useful actions

	/**
     * Action for ACL-test
     *
     * @return array
     */
	public function privatePageAction()
	{
		return array();
	}

	/**
     * Action for ACL-test
     *
     * @return array
     */
	public function personalPageAction()
	{
		return array();
	}

}
