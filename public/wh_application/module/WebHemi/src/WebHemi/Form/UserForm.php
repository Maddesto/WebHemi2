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
	WebHemi\Form\Filter\PurifierFilter,
	WebHemi\Form\Element\PlainText,
	WebHemi\Form\Element\Location,
	WebHemi\Model\User,
	Zend\Form\Fieldset,
	Zend\Form\Element,
	Zend\Validator,
	Zend\I18n\Validator as I18nValidator,
	Zend\Filter as Filter;

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
	/** @var array $allowedAvatarMime */
	protected $allowedAvatarMime = array('image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');
	/** @var string $defaultFormId */
	protected $defaultFormId = 'edituser';

	/**
	 * Class constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		if (empty($name)) {
			$name = $this->defaultFormId;
		}

		parent::__construct($name);

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
						new Filter\StringTrim(),
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
						new Filter\StringTrim(),
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
					'type'      => 'email',
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
					'allow_empty' => false,
					'required'    => true,
					'filters'     => array(
						new Filter\StringTrim(),
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
					'allow_empty' => false,
					'required'    => true,
					'filters'     => array(
						new Filter\StringTrim(),
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
						new Filter\StringTrim(),
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
					'id'          => 'displayname',
					'accesskey'   => 'n',
					'maxlength'   => '255',
					'tabindex'    => self::$tabindex++,
					'placeholder' => 'e.g.: Banana Joe',
				)
			);

		// the headline input
		$headLine = new Element\Text('headline');
		$headLine->setOptions(
				array(
					'filters'    => array(
						new Filter\StringTrim(),
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
					'id'          => 'headline',
					'accesskey'   => 'h',
					'maxlength'   => '255',
					'tabindex'    => self::$tabindex++,
					'placeholder' => 'e.g.: Billionaire genius',
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
						new PurifierFilter(),
						new Filter\StringTrim(),
					),
				)
			)
			->setLabel('Details')
			->setAttributes(
				array(
					'id'          => 'details',
					'accesskey'   => 't',
					'tabindex'    => self::$tabindex++,
					'placeholder' => 'e.g.: I love car racings.',
				)
			);

		$avatarSubFieldset = new Fieldset('avatarInfo');
		$avatarSubFieldset->setLabel('Avatar');

		// the exact value of the avatar property
		$avatar = new Element\Hidden('avatar');

		// the image represented by the avatar
		$avatarImage = new PlainText('avatarimage');
		$avatarImage->setValue('')
			->setAttributes(
				array(
					'id' => 'avatarimage',
				)
			);

		// the type of the avatar
		$avatarType = new Element\Radio('avatartype');
		$avatarType->setOptions(
				array(
					'value_options' => array(
						array(
							'label' => 'Default',
							'value' => User::USER_AVATAR_TYPE_NONE,
							'attributes' => array(
								'accesskey' => 'y',
								'tabindex'  => self::$tabindex++,
							)
						),
						array(
							'label' => 'GR Avatar',
							'value' => User::USER_AVATAR_TYPE_GRAVATAR,
							'attributes' => array(
								'accesskey' => 'g',
								'tabindex'  => self::$tabindex++,
							)
						),
						array(
							'label' => 'File',
							'value' => User::USER_AVATAR_TYPE_BASE64,
							'attributes' => array(
								'accesskey' => 'f',
								'tabindex'  => self::$tabindex++,
							)
						),
						array(
							'label' => 'URL',
							'value' => User::USER_AVATAR_TYPE_URL,
							'attributes' => array(
								'accesskey' => 'l',
								'tabindex'  => self::$tabindex++,
							)
						),
					)
				)
			)
			->setValue(User::USER_AVATAR_TYPE_NONE);

		// GRavatar ID
		$avatarGrId = new Element\Text('avatargrid');
		$avatarGrId->setLabel('GR Avatar ID')
			->setAttributes(
				array(
					'id'          => 'avatargrid',
					'type'        => 'email',
					'accesskey'   => 'a',
					'maxlength'   => '255',
					'tabindex'    => self::$tabindex++,
					'placeholder' => 'e.g.: mike@foo.org',
				)
			);

		// external image location
		$avatarUrl = new Element\Text('avatarurl');
		$avatarUrl->setLabel('Image location')
			->setAttributes(
				array(
					'id'          => 'avatarurl',
					'type'        => 'url',
					'accesskey'   => 'w',
					'maxlength'   => '255',
					'tabindex'    => self::$tabindex++,
					'placeholder' => 'e.g.: http://foo.org/avatar.jpg',
				)
			);

		// file upload
		$avatarFile = new Element\File('avatarfile');
		$avatarFile->setLabel('Upload your avatar')
			->setAttributes(
				array(
					'id'        => 'avatarfile',
					'accesskey' => 'i',
					'tabindex'  => self::$tabindex++,
				)
			);

		// allow to upload file of size at most 100KB
		$avatarFileUpload = new Element\Hidden('MAX_FILE_SIZE');
		$avatarFileUpload->setValue(102400);

		$avatarSubFieldset->add($avatar)
			->add($avatarFileUpload)
			->add($avatarImage)
			->add($avatarType)
			->add($avatarGrId)
			->add($avatarUrl)
			->add($avatarFile);

		$personalInfoFieldset->add($avatarSubFieldset)
				->add($displayName)
				->add($headLine)
				->add($displayEmail)
				->add($details);

		// --- contact fieldset ----------------------------------------------------------------------------------------
		$contactFieldset = new Fieldset('contactInfo');
		$contactFieldset->setLabel('Contact Information');

		// the displayname input
		$phoneNumber = new Element\Text('phonenumber');
		$phoneNumber->setOptions(
				array(
					'filters'    => array(
						new Filter\StringTrim(),
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
			->setLabel('Phone number')
			->setAttributes(
				array(
					'type'      => 'tel',
					'id'        => 'phonenumber',
					'accesskey' => 'n',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
					'placeholder' => 'e.g.: 33 2 123 4567',
					'pattern'   => '^[\d\s]+$',
				)
			);

		// the displayname input
		$location = new Location('location');
		$location->setOptions(
				array(
					'filters'    => array(
						new Filter\StringTrim(),
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
			->setLabel('Location')
			->setAttributes(
				array(
					'id'        => 'location',
					'accesskey' => 'n',
					'maxlength' => '255',
					'tabindex'  => self::$tabindex++,
					'placeholder' => 'e.g.: London, England',
				)
			);

		// the socialnetworks input
		$socialNetworks = new Element\Textarea('socialnetworks');
		$socialNetworks->setOptions(
				array(
					'filters'    => array(
						new Filter\StringTrim(),
					),
				)
			)
			->setLabel('Social Networks')
			->setAttributes(
				array(
					'id'        => 'socialnetworks',
					'accesskey' => 'n',
					'tabindex'  => self::$tabindex++,
				)
			);

		// the websites input
		$websites = new Element\Textarea('websites');
		$websites->setOptions(
				array(
					'filters'    => array(
						new PurifierFilter(),
						new Filter\StringTrim(),
					),
				)
			)
			->setLabel('Websites')
			->setAttributes(
				array(
					'id'        => 'websites',
					'accesskey' => 'w',
					'tabindex'  => self::$tabindex++,
				)
			);

		$contactFieldset->add($phoneNumber)
			->add($location)
			->add($socialNetworks)
			->add($websites);

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
			->add($contactFieldset)
			->add($submit);
	}

	/**
	 * Set data to validate and/or populate elements
	 *
	 * Typically, also passes data on to the composed input filter.
	 *
	 * @param  array|\ArrayAccess|Traversable $data
	 * @return Form|FormInterface
	 * @throws Exception\InvalidArgumentException
	 */
	public function setData($data)
	{
		/* @var $data \Zend\Stdlib\Parameters */
		$avatarInfo  = $data['personalInfo']['avatarInfo'];
		$avatarValue = '';
		$fileName    = $avatarInfo['avatarfile']['tmp_name'];
		$fileType    = $avatarInfo['avatarfile']['type'];

		// this is good for displaying the avatar based on the chosen type whether it is valid (type and size) or not.
		switch ($avatarInfo['avatartype']) {
			case User::USER_AVATAR_TYPE_NONE:
				$data['personalInfo']['avatarInfo']['avatarurl'] = '';
				$data['personalInfo']['avatarInfo']['avatargrid'] = '';
				break;

			case User::USER_AVATAR_TYPE_GRAVATAR:
				$avatarValue = $avatarInfo['avatargrid'];
				$data['personalInfo']['avatarInfo']['avatarurl'] = '';
				break;

			case User::USER_AVATAR_TYPE_URL:
				$avatarValue = $avatarInfo['avatarurl'];
				$data['personalInfo']['avatarInfo']['avatargrid'] = '';
				break;

			case User::USER_AVATAR_TYPE_BASE64:
				// only if the uploaded file is valid for type
				if (
					!empty($fileName) && file_exists($fileName) && is_readable($fileName)
					&& in_array($fileType, $this->allowedAvatarMime)
				) {
					$avatarValue = 'data:' . $fileType . ';base64,' . base64_encode(file_get_contents($fileName));
				}
				// if there were no fileupload but the previus avatar is an uploaded one, we use it
				elseif (strpos($avatarInfo['avatar'], 'data:image') !== false) {
					$avatarValue = $avatarInfo['avatar'];
				}
				break;
		}

		parent::setData($data);

		$this->get('personalInfo')
			->get('avatarInfo')
			->get('avatarimage')
			->setValue($avatarValue);

		$this->get('personalInfo')
			->get('avatarInfo')
			->get('avatar')
			->setValue($avatarValue);

		return $this;
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
			case 'avatarimage':
				$element->setValue(
					$this->getViewRenderer()->avatar(
						$element->getValue(),
						array(),
						array('style' => 'width: 100px; float: left; padding-right: 16px;')
					)
				);
				break;

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
	 * @param Element $formElement
	 * @return bool
	 * @throws Exception\DomainException
	 */
	public function isValid(Element $formElement = null)
	{
		if (empty($formElement)) {
			// no need to validate username and email when no rights to change them
			$this->prepareAccountInfoFieldset();
			// no need to validate password fields, if not given (no change attempt)
			$this->prepareSecurityInfoFieldset();
			// set up avatar accourding to the given type
			$this->prepareAvatar();
			// determine country code and apply validator
			$this->preparePhoneNumber();
		}
		return parent::isValid($formElement);
	}

	/**
	 * Prepare user data for validation
	 */
	protected function prepareAccountInfoFieldset()
	{
		/* @var $acl \WebHemi\Acl\Acl */
		$acl = $this->getAclService();

		// if no rights to change, no need to validate
		if (!$acl->isAllowed('admin/adduser')) {
			$this->get('accountInfo')->get('username')->setOptions(
				array(
					'required'   => false,
					'filters'    => array(),
					'validators' => array()
				)
			);
			$this->get('accountInfo')->get('email')->setOptions(
				array(
					'required'   => false,
					'filters'    => array(),
					'validators' => array()
				)
			);
			$this->get('accountInfo')->get('role')->setOptions(
				array(
					'required'   => false,
					'filters'    => array(),
					'validators' => array()
				)
			);
		}
	}


	/**
	 * Prepare password form elements for validation
	 */
	protected function prepareSecurityInfoFieldset() {
		/* @var $securityFieldset \Zend\Form\Fieldset */
		$securityFieldset = $this->get('securityInfo');
		/* @var $passwordElement \Zend\Form\Element\Password */
		$passwordElement = $securityFieldset->get('password');
		/* @var $confirmElement \Zend\Form\Element\Password */
		$confirmElement = $securityFieldset->get('confirmation');
		// If there were no password change attempt, than we remove the required flag.
		if (
			$this->defaultFormId == $this->getName()
			&& '' == $passwordElement->getValue()
		) {
			$passwordElement->setOptions(
				array(
					'required'    => false,
					'allow_empty' => true,
				)
			);
			$confirmElement->setOptions(
				array(
					'required'    => false,
					'allow_empty' => true,
				)
			);
		}
	}

	/**
	 * Prepare avatar for validation
	 */
	protected function prepareAvatar()
	{
		// Adding filters and validators for the Avatar section
		$avatarType = $this->get('personalInfo')->get('avatarInfo')->get('avatartype')->getValue();
		$avatar     = $this->get('personalInfo')->get('avatarInfo')->get('avatar')->getValue();

		switch ($avatarType) {
			case User::USER_AVATAR_TYPE_BASE64:
				$fileData = $this->get('personalInfo')->get('avatarInfo')->get('avatarfile')->getValue();

				// if the current avatar is not an uploaded one
				if (strpos($avatar, 'data:image') === false) {
					// if no file present, we prevent PHP errors by changing the type
					if(empty($fileData['tmp_name'])) {
						$this->get('personalInfo')->get('avatarInfo')->get('avatartype')->setValue(
							User::USER_AVATAR_TYPE_NONE
						);
					}
				}

				// if there's an uploaded file then we set up the validators
				if(!empty($fileData['tmp_name'])) {
					$this->get('personalInfo')->get('avatarInfo')->get('avatarfile')->setOptions(
						array(
							'required'    => true,
							'allow_empty' => false,
							'validators' => array(
								new Validator\File\UploadFile(),
								new Validator\File\IsImage(),
								new Validator\File\MimeType(
									array('mimeType' => implode(',', $this->allowedAvatarMime))
								),
								new Validator\File\ImageSize(array('maxWidth' => 200, 'maxHeight' => 200))
							)
						)
					);
				}

				break;

			case User::USER_AVATAR_TYPE_URL:
				$this->get('personalInfo')->get('avatarInfo')->get('avatarurl')->setOptions(
					array(
						'required'    => true,
						'allow_empty' => false,
						'filters'    => array(
							new Filter\StringTrim(),
						),
						'validators'  => array(
							new Validator\Uri(
								array(
									'allowRelative' => false,
									'allowAbsolute' => true,
								)
							),
							new Validator\StringLength(
								array(
									'min'      => '11',
									'max'      => '255',
									'encoding' => 'UTF-8'
								)
							),
						),
					)
				);
				break;

			case User::USER_AVATAR_TYPE_GRAVATAR:
				$this->get('personalInfo')->get('avatarInfo')->get('avatargrid')->setOptions(
					array(
						'allow_empty' => true,
						'filters'    => array(
							new Filter\StringTrim(),
						),
						'validators'  => array(
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
									'max'      => '255',
									'encoding' => 'UTF-8'
								)
							),
						),
					)
				);
				break;

			case User::USER_AVATAR_TYPE_NONE:
			default:
				break;
		}
	}

	/**
	 * Prepare phone number for validation
	 */
	protected function preparePhoneNumber()
	{
		// validating phone number if possible
		$phoneNumberElement = $this->get('contactInfo')->get('phonenumber');
		$phoneNumber = preg_replace('/[^\d]/', '', $phoneNumberElement->getValue());
		if (!empty($phoneNumber)) {
			// this database contains only the mutually unambiguous mappings between phone codes and country codes
			$phoneCodeData = include_once APPLICATION_PATH . '/data/phoneCodeToCountryCode.php';

			// if the beginning of the code is in the database then we search for it (no success garantee)
			if (isset($phoneCodeData[$phoneNumber[0]])) {
				$prefix = $phoneNumber[0];
				$countryCode = '';
				for ($i = 0; $i <= 3; $i++) {
					if (isset($phoneCodeData[$phoneNumber[0]][$prefix])) {
						$countryCode = $phoneCodeData[$phoneNumber[0]][$prefix];
						$phoneNumberElement->setOptions(
								array(
									'validators'  => array(
										new I18nValidator\PhoneNumber(
											array(
												'country' => $countryCode,
												'allowed_types' => array('general','mobile')
											)
										)
									),
								)
							)
							->setValue($phoneNumber);
						break;
					}

					if (!isset($phoneNumber[strlen($prefix)])) {
						break;
					}
					$prefix .= $phoneNumber[strlen($prefix)];
				}
			}
		}
	}
}
