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
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Form;

use WebHemi\Form\AbstractForm,
	WebHemi\Form\Filter,
	Zend\Form\Fieldset,
	Zend\Form\Element,
	Zend\Validator,
	Zend\Filter as ZendFilter;

/**
 * User Form
 *
 * @category   WebHemi
 * @package    WebHemi_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserForm extends AbstractForm
{
	/**
	 * Class constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		parent::__construct('user');

		// --- account info filedset -----------------------------------------------------------------------------------
		$accountInfoFieldset = new Fieldset('accountInfo');
		$accountInfoFieldset->setLabel('Account information');

		// the userId
		$userId = new Element\Hidden('user_id');

		// the username input
		$userName = new Element\Text('username');
		$userName->setOptions(
				array(
					'required'   => true,
					'filters'    => array(
						new ZendFilter\StringTrim(),
					),
					'validators' => array(
						new Validator\StringLength(
							array(
							'min'      => '4',
							'max'      => '255',
							'encoding' => 'UTF-8'
							)
						),
						new Validator\Regex('/^[a-z]{1}[a-z0-9\-\_]{3,254}$/i')
					),
				)
			)
			->setLabel('User Name')
			->setAttributes(
				array(
					'id' => 'username',
					'accesskey' => 'u',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
					'pattern'   => '^[a-zA-Z]{1}[a-zA-Z0-9\.\-\_]{3,254}$',
				)
			);

		// the email input
		$email = new Element\Email('email');
		$email->setOptions(
				array(
					'required'   => true,
					'filters'    => array(
						new ZendFilter\StringTrim(),
					),
					'validators' => array(
						new Validator\EmailAddress(
							array(
								'allow' => Validator\Hostname::ALLOW_DNS,
								'useDomainCheck' => true,
								'useMxCheck'     => true,
								'useDeepMxCheck' => true
							)
						),
						new Validator\StringLength(
							array(
								'min'      => '6',
								'max'      => '255',
								'encoding' => 'UTF-8'
							)
						),
						new Validator\Regex('/^[a-z]{1}[a-z0-9\-\_\.]+@[a-z0-9\-\_\.]+\.[a-z]{2,4}$/'),
					),
				)
			)
			->setLabel('Email')
			->setAttributes(
				array(
					'id'        => 'email',
					'accesskey' => 'e',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
					'pattern'   => '^[a-z]{1}[a-z0-9\-\_\.]+@[a-z0-9\-\_\.]+\.[a-z]{2,4}$',
				)
			);

		// the role select box
		$role = new Element\Select('role');
		$role->setOptions(
				array(
					'required'      => true,
					'value_options' => array(
						'member'    => 'Member',
						'moderator' => 'Moderator',
						'editor'    => 'Editor',
						'publisher' => 'Publisher',
						'admin'     => 'Administrator',
					)
				)
			)
			->setLabel('General privilege')
			->setAttributes(
				array(
					'id'        => 'role',
					'accesskey' => 'r',
					'tabindex'  => self::$tabindex++,
				)
			);

		$accountInfoFieldset->add($userId)
			->add($userName)
			->add($email)
			->add($role);

		// --- security info filedset ----------------------------------------------------------------------------------
		$securityInfoFieldset = new Fieldset('securityInfo');
		$securityInfoFieldset->setLabel('Secutity information');

		// the password input
		$password = new Element\Password('password');
		$password->setOptions(
				array(
					'allow_empty' => true,
					'required'    => true,
					'filters'     => array(
						new ZendFilter\StringTrim(),
					),
					'validators'  => array(
						new Validator\StringLength(
							array(
								'min'      => '8',
								'max'      => '255',
								'encoding' => 'UTF-8'
							)
						),
					),
				)
			)
			->setLabel('Change password')
			->setAttributes(
				array(
					'id'        => 'password',
					'accesskey' => 'p',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
					'pattern'   => '^.*{8,255}$',
				)
			);

		// the password confirmation input
		$confirmation = new Element\Password('confirmation');
		$confirmation->setOptions(
				array(
					'allow_empty' => true,
					'required'    => true,
					'filters'     => array(
						new ZendFilter\StringTrim(),
					),
					'validators'  => array(
						new Validator\StringLength(
							array(
								'min'      => '8',
								'max'      => '255',
								'encoding' => 'UTF-8'
							)
						),
						new Validator\Identical(
							array(
								'token' => 'password'
							)
						),
					),
				)
			)
			->setLabel('Confirm password')
			->setAttributes(
				array(
					'id'        => 'password',
					'accesskey' => 'c',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
					'pattern'   => '^.*{8,255}$',
				)
			);


		$securityInfoFieldset->add($password)
				->add($confirmation);

		// --- perosnal info fieldset ----------------------------------------------------------------------------------
		$personalInfoFieldset = new Fieldset('personalInfo');
		$personalInfoFieldset->setLabel('Personal information');

		// the displayname input
		$displayName = new Element\Text('displayname');
		$displayName->setOptions(
				array(
					'filters'    => array(
						new ZendFilter\StringTrim(),
					),
					'validators' => array(
						new Validator\StringLength(
							array(
								'max'      => '255',
								'encoding' => 'UTF-8'
							)
						),
					),
				)
			)
			->setLabel('Display Name')
			->setAttributes(
				array(
					'id'        => 'displayname',
					'accesskey' => 'n',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
				)
			);

		// the headline input
		$headLine = new Element\Text('headline');
		$headLine->setOptions(
				array(
					'filters'    => array(
						new ZendFilter\StringTrim(),
					),
					'validators' => array(
						new Validator\StringLength(
							array(
								'max'      => '255',
								'encoding' => 'UTF-8'
							)
						),
					),
				)
			)
			->setLabel('Headline')
			->setAttributes(
				array(
					'id'        => 'headline',
					'accesskey' => 'h',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
				)
			);

		// the email input
		$displayEmail = new Element\Checkbox('displayemail');
		$displayEmail->setLabel('Show your email address for others?')
			->setOptions(
				array(
					'use_hidden_element' => true,
					'checked_value'      => '1',
					'unchecked_value'    => '0'
				)
			)
			->setAttributes(
				array(
					'id'        => 'displayemail',
					'accesskey' => 'd',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
				)
			);
		
		// the displayname input
		$details = new Element\Textarea('details');
		$details->setOptions(
				array(
					'filters'    => array(
						new ZendFilter\StringTrim(),
						new Filter\PurifierFilter(),
					),
				)
			)
			->setLabel('Details')
			->setAttributes(
				array(
					'id'        => 'details',
					'accesskey' => 't',
					'tabindex'  => self::$tabindex++,
				)
			);

		$personalInfoFieldset->add($displayName)
				->add($headLine)
				->add($displayEmail)
				->add($details);

		// --- rest of the form ----------------------------------------------------------------------------------------

		$submit = new Element\Button('submit');
		$submit->setLabel('Save')
			->setAttributes(
				array(
					'accesskey' => 's',
					'type'      => 'submit',
					'tabindex'  => self::$tabindex++
				)
			);

		$this->add($accountInfoFieldset)
			->add($securityInfoFieldset)
			->add($personalInfoFieldset)
			->add($submit);
	}

	/**
	 * Creates element output for __toString() method
	 *
	 * @param Element $element
	 * @return string
	 */
	protected function renderElement(Element $element)
	{
		$id  = $element->getAttribute('id');
		/* @var $acl \WebHemi\Acl\Acl */
		$acl = $this->getAclService();

		switch ($id) {
			case 'username':
			case 'email':
			case 'role':
				if (!$acl->isAllowed('admin/adduser')) {
					$element->setOptions(
						array(
							'required'   => false,
							'filters'    => array(),
							'validators' => array()
						)
					);
					$element->setAttribute('disabled', 'disabled');
				}
				break;
		}

		return parent::renderElement($element);
	}

	
	/**
	 * Validate the form
	 *
	 * Typically, will proxy to the composed input filter.
	 *
	 * @return bool
	 * @throws Exception\DomainException
	 */
	public function isValid()
	{
		/* @var $securityFieldset \Zend\Form\Fieldset */
		$securityFieldset = $this->get('securityInfo');
		/* @var $passwordElement \Zend\Form\Element\Password */
		$passwordElement = $securityFieldset->get('password');
		/* @var $confirmElement \Zend\Form\Element\Password */
		$confirmElement = $securityFieldset->get('confirmation');
		// If there were no password change attempt, than we remove the required flag.
		if ('' == $passwordElement->getValue()) {
			$passwordElement->setOptions(
				array(
					'required'    => false,
				)
			);
			$confirmElement->setOptions(
				array(
					'required'    => false,
				)
			);
		}
		return  parent::isValid();
	}
}
