<?php

/**
 * WebHemi2
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
 * @category   WebHemi2
 * @package    WebHemi2_Form_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
namespace WebHemi2\Form\View\Helper;

use Zend\Form\View\Helper\FormText;
use Zend\Form\ElementInterface;

/**
 * WebHemi2 Form view helper for the Location element
 *
 * @category   WebHemi2
 * @package    WebHemi2_Form_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class FormLocation extends FormText
{
    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes          = $element->getAttributes();
        $attributes['name']  = $name;
        $attributes['type']  = $this->getType($element);
        $attributes['value'] = $element->getValue();

        $output = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $this->getInlineClosingBracket()
        );

        // if it has value, there's no need for geopositioning
        if (empty($attributes['value'])) {
            $output .= <<<EOH
                <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&key="></script>
                <script type="text/javascript">
                    window.onload = function()
                    {
                        if(navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                handleGetCurrentPosition, handleGetCurrentPositionError
                            );
                        }
                    }

                    function handleGetCurrentPosition(location)
                    {
                        var position     = new google.maps.LatLng(location.coords.latitude, location.coords.longitude);
                        var myGetRequest = new ajaxRequest();
                        var ajaxUrl      = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='
                            + position.lat() + ',' + position.lng()
                            + '&sensor=false';

                        myGetRequest.onreadystatechange = function()
                        {
                            if (myGetRequest.readyState == 4) {
                                if (myGetRequest.status == 200 || window.location.href.indexOf("http") == -1) {
                                    var jsonData = eval("(" + myGetRequest.responseText + ")"),
                                        city = ''
                                        country = '';

                                    if (jsonData.status == 'OK') {
                                        for (var i = 0; i < jsonData.results[0].address_components.length; i++) {
                                            var component = jsonData.results[0].address_components[i];
                                            for (j = 0; j < component.types.length; j++) {
                                                if ('locality' == component.types[j]) {
                                                    city = component.long_name;
                                                }

                                                if ('country' == component.types[j]) {
                                                    country = component.long_name;
                                                }

                                                if ('' != city && '' != country) {
                                                    document.getElementById('{$element->getAttribute('id')}').value
                                                        = city + ', ' + country;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                    jsonData = component = undefined;
                                    console.log(jsonData);
                                }
                            }
                        }

                        myGetRequest.open("GET", ajaxUrl, true);
                        myGetRequest.send(null);
                    }

                    function ajaxRequest(){
                        var activexmodes=["Msxml2.XMLHTTP", "Microsoft.XMLHTTP"];
                        if (window.ActiveXObject) {
                            for (var i=0; i<activexmodes.length; i++) {
                                try {
                                    return new ActiveXObject(activexmodes[i]);
                                }
                                catch(e){
                                    // suppress error
                                }
                            }
                        }
                        else if (window.XMLHttpRequest) {
                            return new XMLHttpRequest();
                        }
                        return false;
                    }

                    function handleGetCurrentPositionError()
                    {
                        // suppress error
                    }

                </script>
EOH;
        }

        return $output;
    }
}
