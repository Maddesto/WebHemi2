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

use Traversable;
use WebHemi2\Form\Element\FabButton;
use WebHemi2\Form\View\Helper\FormFabButton;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Exception;
use Zend\Form\FormInterface;
use Zend\Filter\AbstractFilter;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form\View\Helper\Form as FormHelper;
use Zend\Form\View\Helper\FormLabel;
use Zend\Validator\AbstractValidator;
use Zend\ServiceManager;
use WebHemi2\Acl\Acl;

/**
 * WebHemi2
 *
 * Form Abstraction
 *
 * @category  WebHemi2
 * @package   WebHemi2_Form
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
abstract class AbstractForm extends Form implements ServiceManager\ServiceLocatorAwareInterface
{
    /** @var array $options */
    protected $options;
    /** @var  ServiceManager\ServiceLocatorInterface $serviceLocator */
    protected $serviceLocator;

    /** @staticvar int $tabindex */
    protected static $tabindex = 1;
    /** @staticvar array $validatedElements */
    protected static $validatedForms = [];

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
     *
     * @param boolean $searchInFieldsets   Also search in fieldsets.
     *
     * @return bool
     */
    public function has($elementOrFieldset, $searchInFieldsets = false)
    {
        if ($this->iterator->get($elementOrFieldset) !== null) {
            return true;
        } elseif ($searchInFieldsets) {
            foreach ($this->fieldsets as $fieldset) {
                try {
                    /** @var Fieldset $fieldset */
                    $fieldset->get($elementOrFieldset);
                    return true;
                } catch (Exception\InvalidElementException $e) {
                    // there's no such element here, so we go on.
                    unset($e);
                }
            }
        }

        return false;
    }

    /**
     * Retrieve a named element or fieldset
     *
     * @param string $elementOrFieldset
     *
     * @return Element
     */
    public function get($elementOrFieldset)
    {
        if (!$this->has($elementOrFieldset)) {
            foreach ($this->fieldsets as $fieldset) {
                try {
                    /** @var Fieldset $fieldset */
                    $element = $fieldset->get($elementOrFieldset);
                    return $element;
                } catch (Exception\InvalidElementException $e) {
                    // there's no such element here, so we go on.
                    unset($e);
                }
            }
        } else {
            return $this->iterator->get($elementOrFieldset);
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
     * @param Element $formElement
     *
     * @return bool
     *
     * @throws Exception\DomainException
     */
    public function isValid(Element $formElement = null)
    {
        if (!isset(self::$validatedForms[$this->getName()])) {
            $result = true;
            // if no element specified to validate we go through the entire form
            if (empty($formElement)) {
                // because ZF2 doesn't check everything
                foreach ($this->getFieldsets() as $fieldset) {
                    /* @var Fieldset $fieldset */
                    $result = $this->isValid($fieldset) && $result;
                }

                foreach ($this->getElements() as $element) {
                    /* @var $element \Zend\Form\Element */
                    $result = $this->isValid($element) && $result;
                }

                self::$validatedForms[$this->getName()] = $result;
            } elseif ($formElement instanceof Fieldset) {
                // the fieldsets may contain other fieldsets and elements
                $fieldsetResult = true;
                foreach ($formElement->getFieldsets() as $fieldset) {
                    /* @var $fieldset Fieldset */
                    $fieldsetResult = $this->isValid($fieldset) && $fieldsetResult;
                }

                foreach ($formElement->getElements() as $element) {
                    /* @var $element Element */
                    $fieldsetResult = $this->isValid($element) && $fieldsetResult;
                }
                return $fieldsetResult;
            } else {
                // validate the elements only
                $elementResult = true;
                $validators    = $formElement->getOption('validators');
                $filters       = $formElement->getOption('filters');
                $value         = $formElement->getValue();
                $messages      = [];

                if (!empty($filters)) {
                    // apply all the filter on the value
                    foreach ($filters as $filter) {
                        /** @var AbstractFilter $filter */
                        $value = $filter->filter($value);
                    }
                }

                if (!empty($validators)) {
                    // apply all the validators on the value
                    foreach ($validators as $validator) {
                        /* @var $validator AbstractValidator */
                        if (!$validator->isValid($value)) {
                            $messages = array_merge($messages, $validator->getMessages());
                        }
                    }
                }

                if (!empty($messages)) {
                    $formElement->setMessages($messages);
                    $elementResult = false;
                } else {
                    // Save changes in value
                    $formElement->setValue($value);
                }

                return $elementResult;
            }
        }

        return self::$validatedForms[$this->getName()];
    }

    /**
     * Get validation error messages, if any
     *
     * Returns a hash of element names/messages for all elements failing
     * validation, or, if $elementName is provided, messages for that element
     * only.
     *
     * @param  null|string $elementName
     *
     * @return array|Traversable
     *
     * @throws Exception\InvalidArgumentException
     */
    public function getMessages($elementName = null)
    {
        $messages = [];

        if (null === $elementName) {
            /** @var \Zend\Form\Fieldset $fieldSet */
            foreach ($this->getFieldsets() as $fieldSet) {
                /** @var \Zend\Form\Element $element */
                foreach ($fieldSet->getElements() as $name => $element) {
                    $messageSet = $element->getMessages();
                    if (!is_array($messageSet)
                        && !$messageSet instanceof Traversable
                        || empty($messageSet)
                    ) {
                        continue;
                    }
                    $messages[$name] = $messageSet;
                }
            }
        } else {
            if (!$this->has($elementName)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Invalid element name "%s" provided to %s',
                    $elementName,
                    __METHOD__
                ));
            }
            $element = $this->get($elementName);

            $messageSet = $element->getMessages();
            if ((is_array($messageSet)
                || $messageSet instanceof Traversable)
                && !empty($messageSet)
            ) {
                $messages[$elementName] = $messageSet;
            }
        }

        return $messages;
    }


    /**
     * Retrieve the validated and filtered data
     *
     * @param int $flag
     * @param Element $formElement
     *
     * @return array
     *
     * @throws Exception\DomainException
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED, Element $formElement = null)
    {
        if (!isset(self::$validatedForms[$this->getName()])) {
            throw new Exception\DomainException(sprintf(
                '%s cannot return data as validation has not yet occurred',
                __METHOD__
            ));
        }

        $data = [];

        if (empty($formElement)) {
            foreach ($this->getFieldsets() as $fieldset) {
                /* @var $fieldset \Zend\Form\Fieldset */
                $data[$fieldset->getName()] = $this->getData($flag, $fieldset);
            }

            foreach ($this->getElements() as $element) {
                /* @var $element \Zend\Form\Element */
                $data[$element->getName()] = $this->getData($flag, $element);
            }
        } elseif ($formElement instanceof Fieldset) {
            foreach ($formElement->getFieldsets() as $fieldset) {
                /* @var $fieldset \Zend\Form\Fieldset */
                $data[$fieldset->getName()] = $this->getData($flag, $fieldset);
            }

            foreach ($formElement->getElements() as $element) {
                /* @var $element \Zend\Form\Element */
                $data[$element->getName()] = $this->getData($flag, $element);
            }
        } else {
            return $formElement->getValue();
        }

        return $data;
    }

    /**
     * Prints out form
     */
    public function __toString()
    {
        try {
            $this->prepare();

            $form = $this->getFormHelper()->openTag($this);

            foreach ($this->fieldsets as $fieldset) {
                if ($fieldset instanceof Fieldset) {
                    $form .= $this->renderFieldset($fieldset, $useMDL);
                }
            }

            foreach ($this->elements as $element) {
                if ($element instanceof Element) {
                    $form .= $this->renderElement($element);
                }
            }

            $form .= $this->getFormHelper()->closeTag();
        } catch (\Exception $e) {
            $form = '';
        }

        return $form;
    }

    /**
     * Get element identifier. If not present the name will be used as identifier.
     *
     * @param Element $element
     *
     * @return string
     */
    protected function getElementIdentifier(Element &$element)
    {
        $identifier = $element->getOption('id');

        // if no ID present, we use the name to add one
        if (empty($identifier)) {
            $name = $element->getName();
            $matches = [];

            if (preg_match('/(?:.*\[)?([^\]]+)\]?$/', $name, $matches)) {
                $identifier = $matches[1];
                $element->setAttribute('id', $identifier);
            }
        }

        return $identifier;
    }

    /**
     * Get proper element type
     *
     * @param Element $element
     *
     * @return string
     *
     */
    protected function getElementType(Element $element)
    {
        $type = $element->getAttribute('type');

        // button element fix
        if ($element instanceof FabButton) {
            $type = 'fabbutton';
        } elseif ($element instanceof Element\Button) {
            $type = 'button';
        }

        return $type;
    }

    /**
     * Render error tag for the output
     *
     * @param Element $element
     *
     * @return string
     */
    protected function renderElementError(Element $element)
    {
        $errorTag = '';

        if ($element->getMessages()) {
            $errorTag .= '<div class="error">'
                . $this->getViewRenderer()->formElementErrors($element)
                . '</div>';
        }

        return $errorTag;
    }

    /**
     * Render the form element
     *
     * @param Element $element
     *
     * @return string
     */
    protected function renderElement(Element $element)
    {
        $config = $this->getConfig();
        $useMDL = (bool)$config['view_manager']['theme_settings']['mdl_enabled'];

        return $useMDL
            ? $this->renderMdlElement($element)
            : $this->renderHtmlElement($element);
    }

    /**
     * Creates element output with MDL for __toString() method
     *
     * @param Element $element
     *
     * @return string
     */
    protected function renderMdlElement(Element $element)
    {
        $labelText       = $element->getLabel();
        $identifier      = $this->getElementIdentifier($element);
        $type            = $this->getElementType($element);
        $helper          = 'form' . ucfirst(strtolower($type));
        /** @var PhpRenderer $viewRenderer */
        $viewRenderer    = $this->getViewRenderer();
        /** @var FormLabel $formLabelHelper */
        $formLabelHelper = $viewRenderer->plugin('formLabel');
        $required        = $element->getOption('required');
        $containerClass  = ['element', $type];
        $elementClass    = '';
        $labelClass      = '';

        if ($type != $identifier) {
            $containerClass[] = $identifier;
        }

        if ($required) {
            $element->setAttribute('required', 'required');
        }

        // No need for placeholder with MDL
        if ($element->hasAttribute('placeholder')) {
            $element->removeAttribute('placeholder');
        }

        switch ($type) {
            case 'text':
            case 'email':
            case 'url':
            case 'password':
            case 'location':
            case 'tel':
                $containerClass[] = 'mdl-textfield mdl-js-textfield mdl-textfield--floating-label';
                $elementClass = 'mdl-textfield__input';
                $labelClass = 'mdl-textfield__label';
                break;
            case 'textarea':
                $containerClass[] = 'mdl-textfield mdl-js-textfield mdl-textfield--floating-label';
                $elementClass = 'mdl-textfield__input';
                $labelClass = 'mdl-textfield__label';
                break;
            case 'fabbutton':
                $elementClass = 'mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-button--colored';
                break;
            case 'button':
                $elementClass = 'mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored';
                break;
            case 'checkbox':
                break;
            case 'toggle':
                $labelClass = 'mdl-switch mdl-js-switch mdl-js-ripple-effect';
                $elementClass = 'mdl-switch__input';
                break;
            case 'radio':
                break;
            case 'file':
                break;
        }

        if (!empty($elementClass)) {
            $element->setAttribute('class', $elementClass);
        }

        $openTag  = sprintf('<div class="%s">', implode(' ', $containerClass));
        $closeTag = '</div>' . PHP_EOL;
        $labelOpen = '';
        $labelClose = '';
        $errorTag = $this->renderElementError($element);

        // build label
        if (!empty($labelText)) {
            $labelAttributes = [
                'for' =>  $element->getAttribute('id'),
                'class' => $labelClass
            ];

            $labelOpen  = $formLabelHelper->openTag($labelAttributes);
            $labelClose = $formLabelHelper->closeTag() . PHP_EOL;
        }

        $inputTag = $viewRenderer->$helper($element);

        switch ($type) {
            case 'hidden':
            case 'button':
            case 'submit':
                $tag = $inputTag . $errorTag;
                break;
            case 'fabbutton':
                $tag = $openTag . $labelOpen . $labelText . $labelClose . $inputTag . $errorTag . $closeTag;
                break;
            case 'toggle':
                $tag = $openTag . $labelOpen . $inputTag . '<span class="mdl-switch__label">' . $labelText . '</span>' .
                    $labelClose . $errorTag . $closeTag;
                break;
            case 'checkbox':
            case 'radio':
                $tag = $openTag . $labelOpen . $inputTag . $labelText . $labelClose . $errorTag . $closeTag;
                break;
            case 'file':
                $tag = $openTag . $labelOpen . $labelText . $labelClose . $inputTag . $errorTag . $closeTag;
                break;
            default:
                $tag = $openTag . $labelOpen . $labelText . $labelClose . $inputTag . $errorTag . $closeTag;
                break;
        }

        return $tag . PHP_EOL;
    }

    /**
     * Creates element output for __toString() method
     *
     * @param Element $element
     * @return string
     */
    protected function renderHtmlElement(Element $element)
    {
        $labelText       = $element->getLabel();
        $identifier      = $this->getElementIdentifier($element);
        $type            = $this->getElementType($element);
        $helper          = 'form' . ucfirst(strtolower($type));
        /** @var PhpRenderer $viewRenderer */
        $viewRenderer    = $this->getViewRenderer();
        /** @var FormLabel $formLabelHelper */
        $formLabelHelper = $viewRenderer->plugin('formLabel');
        $required        = $element->getOption('required');
        $containerClass  = ['element', $type];

        if ($type != $identifier) {
            $containerClass[] = $identifier;
        }

        if ($required) {
            $element->setAttribute('required', 'required');
        }

        $openTag  = sprintf('<div class="%s">', implode(' ', $containerClass));
        $closeTag = '</div>' . PHP_EOL;
        $labelOpen = '';
        $labelClose = '';
        $errorTag = $this->renderElementError($element);

        // build label
        if (!empty($labelText)) {
            $labelAttributes = [
                'for' =>  $element->getAttribute('id')
            ];

            $labelOpen  = $formLabelHelper->openTag($labelAttributes);
            $labelClose = $formLabelHelper->closeTag() . PHP_EOL;

            if ($required) {
                $labelClose = '<span class="required">*</span>' . $labelClose;
            }
        }

        $inputTag = $viewRenderer->$helper($element);

        switch ($type) {
            case 'hidden':
            case 'button':
            case 'submit':
                $tag = $inputTag . $errorTag;
                break;
            case 'toggle':
            case 'checkbox':
            case 'radio':
                $tag = $openTag . $labelOpen . $inputTag . $labelText . $labelClose . $errorTag . $closeTag;
                break;
            case 'file':
                $tag = $openTag . $labelOpen . $labelText . $labelClose . $inputTag . $errorTag . $closeTag;
                break;
            default:
                $tag = $openTag . $labelOpen . $labelText . $labelClose . $inputTag . $errorTag . $closeTag;
                break;
        }

        return $tag . PHP_EOL;
    }

    /**
     * Creates fieldset output for __toString() method
     *
     * @param Fieldset $fieldset
     * @param bool     $useMDL
     *
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
        $tag .= sprintf('<fieldset %s>', $this->getFormHelper()->createAttributesString($attributes));
        $tag .= PHP_EOL;

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
     * Retrieve application config
     *
     * @return array|object
     */
    public function getConfig()
    {
        return $this->getServiceLocator()->get('Config');
    }

    /**
     * Retrieve the view renderer instance
     *
     * @return PhpRenderer
     */
    public function getViewRenderer()
    {
        return $this->getServiceLocator()->get('viewmanager')->getRenderer();
    }

    /**
     * Retrieve the form view helper
     *
     * @return FormHelper
     */
    public function getFormHelper()
    {
        return $this->getViewRenderer()->getHelperPluginManager()->get('form');
    }

    /**
     * Retrieve the ACL service instance
     *
     * @return Acl
     */
    public function getAclService()
    {
        return $this->getServiceLocator()->get('acl');
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractForm
     */
    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
