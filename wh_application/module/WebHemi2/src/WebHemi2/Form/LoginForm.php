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
 * @package   WebHemi2_Form
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Form;

use WebHemi2\Model\Table\Lock as UserLockTable;
use Zend\Form\Fieldset;
use Zend\Form\Element;
use Zend\Form\Exception;
use Zend\Validator;
use Zend\Filter;

/**
 * Login Form
 *
 * @category  WebHemi2
 * @package   WebHemi2_Form
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
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

        $submit = new Element\Button('submit');
        $submit->setLabel('Login')
            ->setAttributes(
                [
                    'accesskey' => 's',
                    'type' => 'submit',
                    'tabindex' => self::$tabindex++
                ]
            );

        $url = '/login/';

        if (APPLICATION_MODULE_TYPE == APPLICATION_MODULE_TYPE_SUBDIR && APPLICATION_MODULE != WEBSITE_MODULE) {
            $url = '/' . APPLICATION_MODULE_URI . $url;
        }

        $this->setAttribute('action', $url);
        $this->add($this->getLoginFieldset())
            ->add($submit);

        $this->init();
    }

    /**
     * Retrieve Login fieldset
     *
     * @return Fieldset
     */
    protected function getLoginFieldset()
    {
        $fieldset = new Fieldset('loginInfo');
        $fieldset->setLabel('Login information');

        $identification = new Element\Text('identification');
        $identification->setLabel('Identification')
            ->setOptions(
                [
                    'required' => true,
                    'filters' => [new Filter\StringTrim()],
                    'validators' => [
                        new Validator\StringLength(['min' => 4, 'max' => 255, 'encoding' => 'UTF-8']),
                        new Validator\Regex('/^[a-z]{1}\w+$/i')
                    ],
                ]
            )
            ->setAttributes(
                [
                    'id' => 'identification',
                    'accesskey' => 'u',
                    'maxlength' => 255,
                    'tabindex' => self::$tabindex++,
                    'pattern' => '^[a-z]{1}\w+$',
                    'placeholder' => $identification->getLabel(),
                ]
            );

        $password = new Element\Password('password');
        $password->setLabel('Password')
            ->setOptions(
                [
                    'required' => true,
                    'filters' => [new Filter\StringTrim()],
                    'validators' => [new Validator\StringLength(['min' => 4, 'max' => 255, 'encoding' => 'UTF-8'])],
                ]
            )
            ->setAttributes(
                [
                    'id' => 'password',
                    'accesskey' => 'p',
                    'maxlength' => 255,
                    'tabindex' => self::$tabindex++,
                    'pattern' => '^\w+$',
                    'placeholder' => $password->getLabel()
                ]
            );

        // in ADMIN module there's no way to remember the password or auto-complete the input fields
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
                    [
                        'use_hidden_element' => true,
                        'checked_value' => '1',
                        'unchecked_value' => '0'
                    ]
                )
                ->setAttributes(
                    [
                        'accesskey' => 'r',
                        'id' => 'remember',
                        'tabindex' => self::$tabindex++
                    ]
                );

            $fieldset->add($remember);
        }

        return $fieldset;
    }

    /**
     * Clean up error messages to hide sensitive information from attackers
     */
    public function cleanupMessages()
    {
        $hasErrorMessages = false;
        $element = null;

        /** @var \Zend\Form\Fieldset $fieldSet */
        foreach ($this->getFieldsets() as $fieldSet) {
            /** @var \Zend\Form\Element $element */
            foreach ($fieldSet->getElements() as $element) {
                $messages = $element->getMessages();
                if (!empty($messages)) {
                    $element->setMessages([]);
                    $hasErrorMessages = true;
                }
            }
        }

        if ($hasErrorMessages) {
            // attach the new error message to the last element.
            if ($element instanceof Element) {
                $element->setMessages(['Login attempt failed. Please check all credentials and try again.']);
            }
        }
    }

    /**
     * Validate the form
     *
     * Typically, will proxy to the composed input filter.
     *
     * @param Element $formElement
     * @return bool
     * @throws Exception\DomainException
     */
    public function isValid(Element $formElement = null)
    {
        $isValid = parent::isValid($formElement);
        static $setLock;

        // Ensure that the IP ban counter works also with invalid form post (against DDOS attacks)
        if (!$isValid && !isset($setLock)) {
            /** @var \Zend\Db\Adapter\Adapter $adapter */
            $adapter = $this->getServiceLocator()->get('database');
            $lockTable = new UserLockTable($adapter);
            $lockTable->setLock();
            $setLock = true;
        }

        return $isValid;
    }
}
