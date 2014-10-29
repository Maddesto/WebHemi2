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
 * @package    WebHemi2_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Form;

use WebHemi2\Form\AbstractForm;
use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\Validator;
use Zend\Filter;

/**
 * Login Form
 *
 * @category   WebHemi2
 * @package    WebHemi2_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class LoginForm extends AbstractForm
{
    /**
     * Class constructor
     *
     * @param string $name
     */
    public function __construct($name = null)
    {
        parent::__construct('login');

        // filedset for form elements
        $fieldset = new Fieldset('loginInfo');
        $fieldset->setLabel('Login information');

        // the identification input
        $identification = new Element\Text('identification');
        $identification->setOptions(
            array(
                'required' => true,
                'filters' => array(
                    new Filter\StringTrim(),
                ),
                'validators' => array(
                    new Validator\StringLength(
                        array(
                            'min' => 4,
                            'max' => 255,
                            'encoding' => 'UTF-8'
                        )
                    ),
                    new Validator\Regex('/^[a-z]{1}\w+$/i')
                ),
            )
        )
        ->setLabel('Identification')
        ->setAttributes(
            array(
                'id' => 'identification',
                'accesskey' => 'u',
                'maxlength' => 255,
                'tabindex' => self::$tabindex++,
                'pattern' => '^[a-z]{1}\w+$',
                //'validity' => 'Some alert'
            )
        );

        // password input
        $password = new Element\Password('password');
        $password->setOptions(
            array(
                'required' => true,
                'filters' => array(
                    new Filter\StringTrim(),
                ),
                'validators' => array(
                    new Validator\StringLength(
                        array(
                            'min' => 4,
                            'max' => 255,
                            'encoding' => 'UTF-8'
                        )
                    ),
                ),
            )
        )
        ->setLabel('Password')
        ->setAttributes(
            array(
                'id' => 'password',
                'accesskey' => 'p',
                'maxlength' => 255,
                'tabindex' => self::$tabindex++,
                'pattern' => '^\w+$'
            )
        );


        // in ADMIN module there's no way to remember the password or autocomplete the input fields
        if (APPLICATION_MODULE == ADMIN_MODULE) {
            $identification->setAttribute('autocomplete', 'off');
            $password->setAttribute('autocomplete', 'off');
            $this->setAttribute('autocomplete', 'off');
        }

        $fieldset->add($identification)
                ->add($password);

        // if NOT in ADMIN module, then we supply "remember me" functionality
        if (APPLICATION_MODULE != ADMIN_MODULE) {
            $remember = new Element\Checkbox('remember');
            $remember->setLabel('Remember me')
                ->setOptions(
                    array(
                        'use_hidden_element' => true,
                        'checked_value' => '1',
                        'unchecked_value' => '0'
                    )
                )
                ->setAttributes(
                    array(
                        'accesskey' => 'r',
                        'id' => 'remember',
                        'tabindex' => self::$tabindex++
                    )
                );

            $fieldset->add($remember);
        }

        $submit = new Element\Button('submit');
        $submit->setLabel('Login')
            ->setAttributes(
                array(
                    'accesskey' => 's',
                    'type' => 'submit',
                    'tabindex' => self::$tabindex++
                )
            );

        $url = '/user/login';
        if (APPLICATION_MODULE_TYPE == APPLICATION_MODULE_TYPE_SUBDIR && APPLICATION_MODULE != WEBSITE_MODULE) {
            $url = '/' . APPLICATION_MODULE_URI . $url;
        }

        $this->setAttribute('action', $url);
        $this->add($fieldset)
            ->add($submit);
    }
}
