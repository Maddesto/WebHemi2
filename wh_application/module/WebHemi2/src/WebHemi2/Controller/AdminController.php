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

use Zend\View\Model\ViewModel;
use WebHemi2\Form\AbstractForm;

/**
 * WebHemi2
 *
 * Admin Controller
 *
 * @category  WebHemi2
 * @package   WebHemi2_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 *
 * @method AbstractForm getForm($formName, $name = null) Instantiate a WebHemi2 form
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

        return [];
    }

    /**
     * Login page for admin
     *
     * @return ViewModel
     */
    public function loginAction()
    {
        parent::loginAction();

        /** @var \WebHemi2\Form\LoginForm $form */
        $form = $this->getForm('LoginForm');

        $layout = new ViewModel();
        $layout->setTemplate('layout/login');
        $layout->setVariable('loginForm', $form);
        $layout->setTerminal(true);
        $config = $this->getServiceLocator()->get('Configuration');

        // if we display the login page
        if ($layout instanceof ViewModel) {
            $layout->setVariables(
                [
                    'headerTitle' => $config['headerTitle'],
                    'siteTitle' => $config['siteTitle'],
                    'loginTitle' => $config['loginTitle'],
                    'theme' => isset($config['view_themes']['current_theme'])
                        ? $config['view_themes']['current_theme']
                        : 'default',
                    'useMdl' => $config['view_manager']['use_mdl'],
                ]
            );
        }

        // Cleanup standard input error messages to hide from attackers which data is invalid.
        $form->cleanupMessages();

        return $layout;
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

        return [];
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

        return [];
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

        return [];
    }
}
