<?php

/**
 * WebHemi2
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
 * @category   WebHemi2
 * @package    WebHemi2_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Controller;

use Zend\View\Model\ViewModel;

/**
 * WebHemi2 Admin Controller
 *
 * @category   WebHemi2
 * @package    WebHemi2_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 *
 * @method getForm() getForm(string $formName, string $name) retrieve a WebHemi Form instance with controller plugin
 */
class AdminController extends UserController
{
    /**
     * Default action
     *
     * @return array
     */
    public function indexAction()
    {
        $this->layout()->setVariable('sectionTitle', 'Dashboard');
        $this->layout()->setVariable('sectionClass', 'dashboard');

        return array();
    }

    /**
     * Login page
     *
     * @return ViewModel
     */
    public function loginAction()
    {
        $view = parent::loginAction();

        $config = $this->getServiceLocator()->get('Config');

        // if we display the login page
        if ($view instanceof ViewModel) {
            // TODO: make this an editable config value
            $view->setVariables(
                array(
                    'headerTitle' => 'WebHemi2 Administration Login',
                    'siteTitle' => 'WH Admin',
                    'theme' => isset($config['wh_themes']['current_theme'])
                        ? $config['wh_themes']['current_theme']
                        : 'default',
                )
            );

            // the login page has its built-in layout
            $view->setTerminal(true);
        }

        return $view;
    }

    /**
     * About page.
     *
     * @return array
     */
    public function aboutAction()
    {
        if (APPLICATION_MODULE == ADMIN_MODULE) {
            $this->layout()->setVariable('sectionTitle', 'About');
            $this->layout()->setVariable('sectionClass', 'about');
        }

        return array();
    }

    /**
     * Application page
     *
     * @return array
     */
    public function applicationAction()
    {
        if (APPLICATION_MODULE == ADMIN_MODULE) {
            $this->layout()->setVariable('sectionTitle', 'Application');
            $this->layout()->setVariable('sectionClass', 'application');
        }

        return array();
    }

    /**
     * Control Panel page
     *
     * @return array
     */
    public function controlPanelAction()
    {
        if (APPLICATION_MODULE == ADMIN_MODULE) {
            $this->layout()->setVariable('sectionTitle', 'Control Panel');
            $this->layout()->setVariable('sectionClass', 'control-panel');
        }

        return array();
    }
}
