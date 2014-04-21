<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace WebHemi2\View\Helper;

use Zend\View\Helper\Gravatar,
    Zend\Validator\EmailAddress as EmailValidator;

/**
 * Helper for retrieving avatars from gravatar.com
 */
class Avatar extends Gravatar
{
     /**
     * Image Source
     *
     * @var string
     */
    protected $src;

    /**
     * Returns an avatar image.
     *
     * @param  string|null $email Email address.
     * @param  null|array $options Options
     * @param  array $attribs Attributes for image tag (title, alt etc.)
     * @return Gravatar
     */
    public function __invoke($data = "", $options = array(), $attribs = array())
    {
        $this->email   = null;
        $this->attribs = array();
        $this->src     = '';

        if (!isset($attribs['alt'])) {
            $attribs['alt'] = '';
        }

        if (!isset($attribs['class'])) {
            $attribs['class'] = 'avatar';
        }

        if (!empty($data)) {
            $validator = new EmailValidator();
            if ($validator->isValid($data)) {
                $this->setEmail($data);
            }
            else {
                $this->setSrc($data);
            }
        }

        if (!empty($options)) {
            $this->setOptions($options);
        }

        $this->setAttribs($attribs);
        return $this;
    }

    /**
     * Set image source.
     *
     * @param string $src
     * @return Gravatar
     */
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    /**
     * Retrieve image source.
     *
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set src attrib for image.
     *
     * You shouldn't set a own url value!
     * It sets value, uses protected method getAvatarUrl.
     *
     * If already exists, it will be overwritten.
     *
     * @return void
     */
    protected function setSrcAttribForImg()
    {
        $attribs = $this->getAttribs();
        $src     = $this->getSrc();

        if (empty($src)) {
            $attribs['src'] = $this->getAvatarUrl() . '&rnd=' . rand(1000, 9999);
        }
        else {
            $attribs['src'] = $src;
        }

        $this->setAttribs($attribs);
    }
}