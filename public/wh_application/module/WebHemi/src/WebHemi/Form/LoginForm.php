<?php

namespace WebHemi\Form;

use WebHemi\Form\AbstractForm;

class LoginForm extends AbstractForm
{

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
