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
 * @package    WebHemi_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Form;

use WebHemi\Form\AbstractForm,
	WebHemi\Application,
	Zend\Form\Fieldset,
	Zend\Form\Element,
	Zend\Validator,
	Zend\Filter;

/**
 * Login Form
 *
 * @category   WebHemi
 * @package    WebHemi_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
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

		// the username input
		$username = new Element\Text('username');
		$username->setOptions(array(
					'required'   => true,
					'filters'    => array(
						new Filter\StringTrim(),
					),
					'validators' => array(
						new Validator\StringLength(4, 255, true),
						new Validator\Regex('/^[a-z]{1}\w+$/i')
					),
				))
				->setLabel('Username')
				->setAttribute('id', 'username')
				->setAttribute('accesskey', 'u')
				->setAttribute('maxlength', '255')
				->setAttribute('tabindex', self::$tabindex++);

		// password input
		$password = new Element\Password('password');
		$password->setOptions(array(
					'required'   => true,
					'filters'    => array(
						new Filter\StringTrim(),
					),
					'validators' => array(
						new Validator\StringLength(8, 255, true),
					),
				))
				->setLabel('Password')
				->setAttribute('id', 'password')
				->setAttribute('accesskey', 'p')
				->setAttribute('maxlength', '255')
				->setAttribute('tabindex', self::$tabindex++);

		$fieldset->add($username)
				->add($password);

		// in ADMIN module there's no way to
		if (APPLICATION_MODULE == Application::ADMIN_MODULE) {
			$this->setAttribute('autocomplete=', 'off');
		}
		// otherwise we supply "remember me" functionality
		else {
			$remember = new Element\Checkbox('remember');
			$remember->setLabel('Remember me')
					->setOptions(array(
						'use_hidden_element' => true,
						'checked_value'      => 1,
						'unchecked_value'    => 0

					))
					->setAttribute('accesskey', 'r')
					->setAttribute('id', 'remember')
					->setAttribute('tabindex', self::$tabindex++);
			$fieldset->add($remember);
		}

		$submit = new Element\Submit('submit');
		$submit->setValue('Login')
				->setAttribute('accesskey', 's')
				->setAttribute('tabindex', self::$tabindex++);

		$this->setAttribute('action', '/user/login')
				->add($fieldset)
				->add($submit);
	}
}
