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

use Zend\Form\Form,
	Zend\Form\Element,
	Zend\Form\Fieldset,
	Zend\Form\Exception,
	Zend\View\Renderer\PhpRenderer,
	Zend\ServiceManager\ServiceManagerAwareInterface,
	Zend\ServiceManager\ServiceManager,
	WebHemi\Acl\Acl;

/**
 * WebHemi Form Abstraction
 *
 * @category   WebHemi
 * @package    WebHemi_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
abstract class AbstractForm extends Form implements ServiceManagerAwareInterface
{
	/** @var array $options */
	protected $options;
	/** @var ServiceManager $serviceManager */
	protected $serviceManager;

	/** @staticvar int $tabindex */
	protected static $tabindex = 1;
	/** @staticvar array $validatedElements */
	protected static $validatedForms = array();

	/**
	 * Class constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		parent::__construct($name);

		$token = new Element\Csrf('token');
		$this->add($token);
	}

	/**
	 * Does the fieldset have an element/fieldset by the given name?
	 *
	 * @param string  $elementOrFieldset   The name of the element
	 * @param boolean $searchInFieldsets   Also search in fieldsets.
	 * @return bool
	 */
	public function has($elementOrFieldset, $searchInFieldsets = false)
	{
		if (array_key_exists($elementOrFieldset, $this->byName)) {
			return true;
		}
		elseif ($searchInFieldsets) {
			foreach ($this->fieldsets as $fieldset) {
				try {
					$fieldset->get($elementOrFieldset);
					return true;
				}
				catch (Exception\InvalidElementException $e) {
					// there's no such element here, so we go on.
				}
			}
		}

		return false;
	}

	/**
	 * Retrieve a named element or fieldset
	 *
	 * @param  string $elementOrFieldset
	 * @return ElementInterface
	 */
	public function get($elementOrFieldset)
	{
		if (!$this->has($elementOrFieldset)) {
			foreach ($this->fieldsets as $fieldset) {
				try {
					$element = $fieldset->get($elementOrFieldset);
					return $element;
				}
				catch (Exception\InvalidElementException $e) {
					// there's no such element here, so we go on.
				}
			}
		}
		else {
			return $this->byName[$elementOrFieldset];
		}

		throw new Exception\InvalidElementException(
			sprintf(
				"No element by the name of [%s] found in form",
				$elementOrFieldset
			)
		);
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
		// @TODO: find out why is this method called twice.
		if (!isset(self::$validatedForms[$this->getName()])) {
			$result = parent::isValid();

			// because ZF2 doesn't check everything
			if ($result) {
				$result = $this->validateFieldsets($this->fieldsets)
					&& $this->validateElements($this->elements);
			}
			self::$validatedForms[$this->getName()] = $result;
		}
		
		return self::$validatedForms[$this->getName()];
	}

	/**
	 * Validate form fieldsets
	 *
	 * @param array $fieldsets
	 * @return boolean
	 */
	protected function validateFieldsets(array $fieldsets)
	{
		$result = true;

		/* @var $fieldset Fieldset */
		foreach ($fieldsets as $fieldset) {
			$subFieldsets = $fieldset->getFieldsets();

			// if there are sub fieldsets, we also validate them
			if (!empty($subFieldsets)) {
				$result = $this->validateFieldsets($subFieldsets) && $result;
			}
			// otherwise we validate the elements
			else {
				$result = $this->validateElements($fieldset->getElements()) && $result;
			}
		}

		return $result;
	}
	/**
	 * Apply filters and validate form elements.
	 *
	 * @param array $elements
	 * @return boolean
	 */
	protected function validateElements(array $elements)
	{
		$result = true;
		/* @var $element Element */
		foreach ($elements as $element) {
			$validators = $element->getOption('validators');
			$filters    = $element->getOption('filters');
			
			$value    = $element->getValue();
			$messages = array();
			
			if (!empty($filters)) {
				// apply all the filter on the value
				foreach ($filters as $filter) {
					/* @var $filter WebHemi\Form\Filter\PurifierFilter */
					if ($filter instanceof \WebHemi\Form\Filter\PurifierFilter) {
						$filter->setServiceManager($this->getServiceManager());
					}
					$value = $filter->filter($value);
				}
			}

			if (!empty($validators)) {
				// apply all the validators on the value
				foreach ($validators as $validator) {
					/* @var $validator Zend\Validator\AbstractValidator */
					if (!$validator->isValid($value)) {
						$messages = array_merge($messages, $validator->getMessages());
					}
				}
			}

			if (!empty($messages)) {
				$element->setMessages($messages);
				$result = false;
			}
			else {
				// Save changes in value
				$element->setValue($value);
			}
		}
		return $result;
	}

	/**
	 * Prints out form
	 */
	public function __toString()
	{
		try {
			$this->prepare();

			$form = $this->getViewRenderer()->form()->openTag($this);

			foreach ($this->fieldsets as $fieldset) {
				if ($fieldset instanceof Fieldset) {
					$form .= $this->renderFieldset($fieldset);
				}
			}

			foreach ($this->elements as $element) {
				if ($element instanceof Element) {
					$form .= $this->renderElement($element);
				}
			}

			$form .= $this->getViewRenderer()->form()->closeTag($this);
		} 
		catch (\Exception $ex) {
			dump($ex->__toString());
			$form = '';
		}

		return $form;
	}

	/**
	 * Creates element output for __toString() method
	 *
	 * @param Element $element
	 * @return string
	 */
	protected function renderElement(Element $element)
	{
		$label      = $element->getLabel();
		$id         = $element->getOption('id');

		// if no ID present, we use the name to add one
		if (empty($id)) {
			$name = $element->getName();
			$matches = array();

			if (preg_match('/(?:.*\[)?([^\]]+)\]?$/', $name, $matches)) {
				$id = $matches[1];
				$element->setAttribute('id', $id);
			}
		}

		$required   = $element->getOption('required');
		$type       = $element->getAttribute('type');

		// button element fix
		if ($element instanceof Element\Button) {
			$type = 'button';
		}

		if ($type == $id) {
			$id = '';
		}

		$class = trim("element {$type} {$id}");

		$openTag    = sprintf('<div class="%s">', $class);
		$closeTag   = '</div>' . PHP_EOL;
		$labelTag   =
		$errorTag   =
		$tag        = '';

		// build label
		if ($required || !empty($label)) {
			$formLabel = $this->getViewRenderer()->plugin('formLabel');
			$labelTag  = $formLabel->openTag($element);
			$labelTag .= $label;

			if ($required) {
				$labelTag .= '<span class="required">*</span>';
			}

			$labelTag .= $formLabel->closeTag() . PHP_EOL;
		}

		// build errors
		if ($element->getMessages()) {
			$errorTag = '<div class="error">'
					. $this->getViewRenderer()->formElementErrors($element)
					. '</div>';
		}

		$helper = 'form' . ucfirst(strtolower($type));
		$inputTag = $this->getViewRenderer()->$helper($element);

		switch($type) {
			case 'hidden':
			case 'button':
			case 'submit':
				$tag = $inputTag . $errorTag;
				break;
			case 'checkbox':
			case 'radio':
				$tag = $openTag . $inputTag . $labelTag . $errorTag . $closeTag;
				break;
			case 'file':
				// @TODO: create hidden input for MAX_FILE_SIZE
				$tag = $openTag . $labelTag . $inputTag . $errorTag . $closeTag;
				break;
			default:
				$tag = $openTag . $labelTag . $inputTag . $errorTag . $closeTag;
				break;
		}

		return $tag . PHP_EOL;
	}

	/**
	 * Creates fieldset output for __toString() method
	 *
	 * @param Fieldset $fieldset
	 * @return string
	 */
	protected function renderFieldset(Fieldset $fieldset)
	{
		$tag          = '';
		$subFieldsets = $fieldset->getFieldsets();
		$attributes   = $fieldset->getAttributes();
		$elements     = $fieldset->getElements();
		$label        = $fieldset->getLabel();

		// open tag
		$tag .= sprintf('<fieldset %s>', $this->getViewRenderer()->form()->createAttributesString($attributes)) . PHP_EOL;

		// if there is label, we render it as legend
		if (!empty($label)) {
			$tag .= sprintf('<legend>%s</legend>', $label) . PHP_EOL;
		}

		// if there are sub fieldsets, we render them
		if (!empty($subFieldsets)) {
			foreach ($subFieldsets as $subFieldset) {
				$tag .= $this->renderFieldset($subFieldset);
			}
		}

		// render each element
		foreach ($elements as $element) {
			if ($element instanceof Element) {
				$tag .= $this->renderElement($element);
			}
		}

		// close tag and return
		return $tag . '</fieldset>' . PHP_EOL;
	}

	/**
	 * Retrieve the view renderer instance
	 *
	 * @return PhpRenderer
	 */
	public function getViewRenderer()
	{
		return $this->getServiceManager()->get('viewmanager')->getRenderer();
	}

	/**
	 * Retrieve the ACL service instance
	 *
	 * @return Acl
	 */
	public function getAclService()
	{
		return $this->getServiceManager()->get('acl');
	}

	/**
	 * Retrieve service manager instance
	 *
	 * @return ServiceManager
	 */
	public function getServiceManager()
	{
		return $this->serviceManager;
	}

	/**
	 * Set service manager instance
	 *
	 * @param ServiceManager $locator
	 * @return void
	 */
	public function setServiceManager(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
	}
}
