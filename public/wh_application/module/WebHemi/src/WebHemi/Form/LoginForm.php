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

use WebHemi\Form\AbstractForm;

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
		parent::__construct('product');

		$this->add(array(
			'name' => 'username',
			'type' => 'Zend\Form\Element\Text',
			'attributes' => array(
				'id' => 'username',
			),
			'options' => array(
				'label' => 'Your Name',
				'required' => true,
				'filters' => array(
					array('StringTrim')
				),
				'validators' => array(
					array(
						'StringLength',
						true,
						array(
							6,
							999
						)
					)
				),
			),
		));

		$this->add(array(
			'name' => 'password',
			'type' => 'Zend\Form\Element\Password',
			'attributes' => array(
				'id' => 'password',
			),
			'options' => array(
				'label' => 'Password',
				'required' => true,
				'filters' => array(
					array('StringTrim')
				),
				'validators' => array(
					array(
						'StringLength',
						true,
						array(
							6,
							999
						)
					)
				),
			),
		));

		$this->add(array(
			'name' => 'token',
			'type' => '\Zend\Form\Element\Csrf',
		));

		$this->add(array(
			'name' => 'submit',
			'type' => 'Zend\Form\Element\Submit',
			'attributes' => array(
				'value' => 'Login',
			),
		));
	}

}
