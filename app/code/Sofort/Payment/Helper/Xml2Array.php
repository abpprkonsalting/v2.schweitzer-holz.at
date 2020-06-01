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

class Xml2Array extends AbstractHelper
{
    protected $xml = null;
    protected $encoding = 'UTF-8';
    protected $prefix_attributes = '@';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public function init($version = '1.0', $encoding = 'UTF-8', $format_output = true)
    {
        $this->xml = new \DOMDocument($version, $encoding);
        $this->xml->formatOutput = $format_output;
        $this->encoding = $encoding;
    }

    /**
     * Convert an XML to Array
     * @param string $node_name - name of the root node to be converted
     * @param int - Bitwise OR of the libxml option constants see @link http://php.net/manual/zh/libxml.constants.php
     * @param array $arr - aray to be converterd
     * @return DOMDocument
     */
    public function &createArray($input_xml, $options = 0)
    {
        $xml = $this->getXMLRoot();
        if(is_string($input_xml)) {
            $parsed = $xml->loadXML($input_xml, $options);
            if(!$parsed) {
                throw new \Exception('[XML2Array] Error parsing the XML string.');
            }
        } else {
            if(get_class($input_xml) != 'DOMDocument') {
                throw new \Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
            }
            $xml = $this->xml = $input_xml;
        }
        $array[$xml->documentElement->tagName] = $this->convert($xml->documentElement);
        // clear the xml node in the class for 2nd time use.
        $this->xml = null;
        return $array;
    }

    /**
     * Convert an Array to XML
     * @param mixed $node - XML as a string or as an object of DOMDocument
     * @return mixed
     */
    protected function &convert($node) {
        $output = array();

        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
                $output[$this->prefix_attributes.'cdata'] = trim($node->textContent);
                break;

            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:

                // for each child node, call the covert function recursively
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->convert($child);
                    if(isset($child->tagName)) {
                        $t = $child->tagName;

                        // avoid fatal error if the content looks like '<html><body>You are being <a href="https://some.url">redirected</a>.</body></html>'
                        if(isset($output) && !is_array($output)) {
                            continue;
                        }
                        // assume more nodes of same kind are coming
                        if(!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if($v !== '') {
                            $output = $v;
                        }
                    }
                }

                if(is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if(is_array($v) && count($v)==1) {
                            $output[$t] = $v[0];
                        }
                    }
                    if(empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }

                // loop through the attributes and collect them
                if($node->attributes->length) {
                    $a = array();
                    foreach($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if(!is_array($output)) {
                        $output = array($this->prefix_attributes.'value' => $output);
                    }
                    $output[$this->prefix_attributes.'attributes'] = $a;
                }
                break;
        }
        return $output;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    protected function getXMLRoot(){
        if(empty($this->xml)) {
            $this->init();
        }
        return $this->xml;
    }
}
