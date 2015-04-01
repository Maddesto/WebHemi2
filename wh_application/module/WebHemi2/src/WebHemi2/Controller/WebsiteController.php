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
 * @package   WebHemi2_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Controller;

/**
 * WebHemi2
 *
 * Website Controller
 *
 * @category  WebHemi2
 * @package   WebHemi2_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class WebsiteController extends AbstractController
{
    /**
     * Main page
     *
     * @return array
     */
    public function indexAction()
    {
        $this->getUserAuth()->hasIdentity();
        return [];
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
        dump($category);
        dump($id);
        dump($format);
        return [];
    }
}
