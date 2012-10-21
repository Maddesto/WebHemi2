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

use Zend\Form\Form,
	Zend\Form\Element,
	Zend\Form\Fieldset,
	Zend\View\Renderer\PhpRenderer,
	Zend\ServiceManager\ServiceManagerAwareInterface,
	Zend\ServiceManager\ServiceManager;

/**
 * WebHemi Form Abstraction
 *
 * @category   WebHemi
 * @package    WebHemi_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
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
     * Retrieve a named element or fieldset
     *
     * @param  string $elementOrFieldset
     * @return ElementInterface
     */
    public function get($elementOrFieldset)
    {
        if (!$this->has($elementOrFieldset)) {
			foreach ($this->fieldsets as $fieldset) {
				$element = $fieldset->get($elementOrFieldset);
				if (!is_null($element)) {
					break;
				}
			}
        }
        else {
			$element = $this->byName[$elementOrFieldset];
		}

		return $element;
    }

	/**
	 * Prints out form
	 */
	public function __toString()
	{
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

			if (preg_match('/(?:.*\[)?([^\]]+)/', $name, $matches)) {
				$id = $matches[1];
				$element->setAttribute('id', $id);
			}
		}

		$required   = $element->getOption('required');
		$type       = $element->getAttribute('type');
		$openTag    = sprintf('<div class="element %s %s">', $type, $id);
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
				$tag = $inputTag . PHP_EOL;
				break;
			case 'checkbox':
			case 'radio':
				$tag = $openTag . $inputTag . $labelTag . $errorTag . $closeTag . PHP_EOL;
				break;
			case 'file':
				// @TODO: create hidden input for MAX_FILE_SIZE
				$tag = $openTag . $labelTag . $inputTag . $errorTag . $closeTag . PHP_EOL;
				break;
			default:
				$tag = $openTag . $labelTag . $inputTag . $errorTag . $closeTag . PHP_EOL;
				break;
		}

		return $tag;
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