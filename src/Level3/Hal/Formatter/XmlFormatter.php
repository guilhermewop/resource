<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 * (c) Ben Longden <ben@nocarrier.co.uk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Hal\Formatter;

use Level3\Hal\Resource;
use SimpleXMLElement;

class XmlFormatter extends Formatter
{
    protected $contentType = FormatterFactory::CONTENT_TYPE_APPLICATION_HAL_XML;

    public function formatResource(Resource $resource, $pretty)
    {
        $doc = new SimpleXMLElement('<resource></resource>');
        if (!is_null($resource->getUri())) {
            $doc->addAttribute('href', $resource->getUri());
        }
        $this->linksForXml($doc, $resource->getLinks());

        $this->arrayToXml($resource->getData(), $doc);

        foreach($resource->getResources() as $rel => $resources) {
            $this->resourcesForXml($doc, $rel, $resources);
        }

        $dom = dom_import_simplexml($doc);
        if ($pretty) {
            $dom->ownerDocument->preserveWhiteSpace = false;
            $dom->ownerDocument->formatOutput = true;
        }
        return $dom->ownerDocument->saveXML();
    }

    /**
     * linksForXml
     * Add links in hal+xml format to a SimpleXmlElement object
     *
     * @param \SimpleXmlElement $doc
     * @param HalLinkContainer $links
     * @return void
     */
    protected function linksForXml(SimpleXmlElement $doc, Array $links)
    {
        foreach($links as $rel => $links) {
            foreach ($links as $link) {
                $element = $doc->addChild('link');
                $element->addAttribute('rel', $rel);
                $element->addAttribute('href', $link->getHref());
                foreach ($link->getAttributes() as $attribute => $value) {
                    $element->addAttribute($attribute, $value);
                }
            }
        }
    }

    /**
     * arrayToXml
     *
     * @param array $data
     * @param \SimpleXmlElement $element
     * @param mixed $parent
     * @access protected
     * @return void
     */
    protected function arrayToXml(array $data, \SimpleXmlElement $element, $parent=null)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    if (count($value) > 0 && isset($value[0])) {
                        $this->arrayToXml($value, $element, $key);
                    } else {
                        $subnode = $element->addChild($key);
                        $this->arrayToXml($value, $subnode, $key);
                    }
                } else {
                    $subnode = $element->addChild($parent);
                    $this->arrayToXml($value, $subnode, $parent);
                }
            } else {
                if (!is_numeric($key)) {
                    if (substr($key, 0, 1) === '@') {
                        $element->addAttribute(substr($key, 1), $value);
                    } elseif($key === 'value' and count($data) === 1) {
                        $element->{0} = $value;
                    } elseif(is_bool($value)) {
                        $element->addChild($key, intval($value));
                    } else {
                        $element->addChild($key, htmlspecialchars($value, ENT_QUOTES));
                    }
                } else {
                    $element->addChild($parent, htmlspecialchars($value, ENT_QUOTES));
                }
            }
        }
    }

    /**
     * resourcesForXml
     * Add resources in hal+xml format (identified by $rel) to a SimpleXmlElement object
     *
     * @param \SimpleXmlElement $doc
     * @param mixed $rel
     * @param array $resources
     */
    protected function resourcesForXml(\SimpleXmlElement $doc, $rel, array $resources)
    {
        foreach($resources as $resource) {

            $element = $doc->addChild('resource');
            $element->addAttribute('rel', $rel);

            if ($resource) {
                if (!is_null($resource->getUri())) {
                    $element->addAttribute('href', $resource->getURI());
                }

                $this->linksForXml($element, $resource->getLinks());

                foreach($resource->getResources() as $innerRel => $innerRes) {
                    $this->resourcesForXml($element, $innerRel, $innerRes);
                }

                $this->arrayToXml($resource->getData(), $element);
            }
        }
    }
}