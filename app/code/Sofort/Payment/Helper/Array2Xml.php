<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Array2Xml extends AbstractHelper
{
    /**
     * @var \DOMDocument
     */
    private $xml = null;
    private $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public function init($version = '1.0', $encoding = 'UTF-8', $format_output = true)
    {
        $this->xml = new \DomDocument($version, $encoding);
        $this->xml->formatOutput = $format_output;
        $this->encoding = $encoding;
    }

    /**
     * Convert an Array to XML
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DomDocument
     */
    public function &createXML($node_name, $arr = array())
    {
        $xml = $this->getXMLRoot();
        $xml->appendChild($this->convert($node_name, $arr));

        // clear the xml node in the class for 2nd time use.
        $this->xml = null;
        return $xml;
    }

    /**
     * Convert an Array to XML.
     *
     * @param string $node_name
     *   Name of the root node to be converted.
     * @param array $arr
     *  Array to be converted.
     *
     * @throws \Exception
     *
     * @return \DOMNode
     */
    private function &convert($node_name, $arr = array())
    {

        $xml = $this->getXMLRoot();
        $node = $xml->createElement($node_name);

        if (is_array($arr)) {
            // get the attributes first
            if (isset($arr['@attributes'])) {
                foreach ($arr['@attributes'] as $key => $value) {
                    if (!$this->isValidTagName($key)) {
                        throw new \Exception(
                            '[Array2XML] Illegal character in attribute name. attribute: '
                            . $key
                            . ' in node: '
                            . $node_name
                        );
                    }
                    $node->setAttribute($key, $this->bool2str($value));
                }
                //remove the key from the array once done.
                unset($arr['@attributes']);
            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if (isset($arr['@value'])) {
                $node->appendChild($xml->createTextNode($this->bool2str($arr['@value'])));
                //remove the key from the array once done.
                unset($arr['@value']);
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } elseif (isset($arr['@cdata'])) {
                $node->appendChild($xml->createCDATASection($this->bool2str($arr['@cdata'])));
                //remove the key from the array once done.
                unset($arr['@cdata']);
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            } elseif (isset($arr['@xml'])) {
                $fragment = $xml->createDocumentFragment();
                $fragment->appendXML($arr['@xml']);
                $node->appendChild($fragment);
                unset($arr['@xml']);
                return $node;
            }
        }

        //create subnodes using recursion
        if (is_array($arr)) {
            // recurse to get the node for that key
            foreach ($arr as $key => $value) {
                if (!$this->isValidTagName($key)) {
                    throw new \Exception(
                        '[Array2XML] Illegal character in tag name. tag: ' . $key . ' in node: ' . $node_name
                    );
                }
                if (is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach ($value as $k => $v) {
                        $node->appendChild($this->convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild($this->convert($key, $value));
                }
                //remove the key from the array once done.
                unset($arr[$key]);
            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if (!is_array($arr)) {
            $node->appendChild($xml->createTextNode($this->bool2str($arr)));
        }

        return $node;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private function getXMLRoot()
    {
        if (empty($this->xml)) {
            $this->init();
        }
        return $this->xml;
    }

    /*
     * Get string representation of boolean value
     */
    private function bool2str($v)
    {
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;
        return $v;
    }

    /*
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn
     */
    private function isValidTagName($tag)
    {
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}
