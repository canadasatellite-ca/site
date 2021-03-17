<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Converter;

use \Aitoc\OrdersExportImport\Model\Profile;

/**
 * Class XML
 * @package Aitoc\OrdersExportImport\Model\Converter
 */
class XML extends \Aitoc\OrdersExportImport\Model\Converter
{
    /**
     * @var SimpleXml
     */
    private $xml;

    /**
     * @param $filename
     * @return array
     */
    public function toFile($filename)
    {
        $config     = $this->getConfig();
        $collection = $this->filter();
        $xml        = $this->appendCollection($collection);
        if (!$config['path_local']) {
            $config['path_local'] = 'var/tmp/';
        }
        $this->publish->setPath($config['path_local']);
        $this->publish->publish($filename, $xml->asXML());
        return [$this->publish->getPathFile(),$this->publish->getFilename()];
    }

    /**
     * @return SimpleXml|\SimpleXMLElement
     */
    public function getXml()
    {
        if (!$this->xml) {
            $this->xml = new \SimpleXMLElement('<orders></orders>');
        }

        return $this->xml;
    }

    /**
     * @param $collection
     * @return SimpleXml|\SimpleXMLElement
     */
    public function appendCollection($collection)
    {
        $counts  = round(90 / $collection->count(), 1);
        $percent = 10;
        $xml     = $this->getXml();
        foreach ($this->partCollection($collection) as $element) {
            $order     = $xml->addChild('order');
            $dataOrder = simplexml_load_string($this->toXml($element, 'fields'));
            $this->xmlAppend($order, $dataOrder);

            foreach ($this->getEntities($element) as $label => $collect) {
                if (count($collect)) {
                    if (!isset($collect['inner'])) {
                        foreach ($collect as $code => $lines) {
                            $innerXML = simplexml_load_string("<{$code}></{$code}>");
                            foreach ($lines['collection'] as $key => $item) {
                                $data = simplexml_load_string($this->toXml($item, $lines['item']));
                                $this->xmlAppend($innerXML, $data);
                            }
                            $this->xmlAppend($order, $innerXML);
                        }
                    } else {
                        $innerXML = simplexml_load_string("<{$label}></{$label}>");
                        foreach ($collect['elements'] as $item) {
                            $elXML = simplexml_load_string("<{$collect['item']}></{$collect['item']}>");
                            $addXml = simplexml_load_string($this->toXml($item['element'], 'fields'));
                            $this->xmlAppend($elXML, $addXml);
                            foreach ($item['inner'] as $method => $element) {
                                $methodXml = simplexml_load_string("<{$method}></{$method}>");
                                foreach ($element['collection'] as $record) {
                                    $this->xmlAppend(
                                        $methodXml,
                                        simplexml_load_string($this->toXml($record, $element['item']))
                                    );
                                }
                                $this->xmlAppend($elXML, $methodXml);
                            }

                            $this->xmlAppend($innerXML, $elXML);
                        }
                        $this->xmlAppend($order, $innerXML);
                    }
                }
            }
            $percent += $counts;
            $stack = $this->getStack();
            $stack->setPercent($percent);
            $stack->save();
        }

        return $xml;
    }

    /**
     * @param \SimpleXMLElement $to
     * @param \SimpleXMLElement $from
     */
    public function xmlAppend(\SimpleXMLElement $to, \SimpleXMLElement $from)
    {
        $toDom   = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

    /**
     * @param $element
     * @param string $rootName
     * @param array $keys
     * @param bool $addOpenTag
     * @param bool $addCdata
     * @return string
     */
    public function toXml($element, $rootName = 'item', array $keys = [], $addOpenTag = false, $addCdata = true)
    {
        $xml  = '';
        $data = $element->toArray($keys);
        foreach ($data as $fieldName => $fieldValue) {
            if (is_array($fieldValue) || is_object($fieldValue)) {
                $xml .= $this->divArray($fieldValue);
            } else {
                if ($addCdata === true) {
                    $fieldValue = "<![CDATA[{$fieldValue}]]>";
                } else {
                    $fieldValue = str_replace(
                        ['&', '"', "'", '<', '>'],
                        ['&amp;', '&quot;', '&apos;', '&lt;', '&gt;'],
                        $fieldValue
                    );
                }
                $xml .= "<{$fieldName}>{$fieldValue}</{$fieldName}>\n";
            }
        }
        if ($rootName) {
            $xml = "<{$rootName}>\n{$xml}</{$rootName}>\n";
        }
        if ($addOpenTag) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
        }

        return $xml;
    }

    /**
     * @param $array
     * @return string
     */
    public function divArray($array)
    {
        $xml = '';
        foreach ($array as $key => $item) {
            if (is_array($item)) {
                if (is_int($key)) {
                    $xml .= "<attr id=\"{$key}\">\n";
                } else {
                    $xml .= "<{$key}>\n";
                }

                $xml .= $this->divArray($item);
                if (is_int($key)) {
                    $xml .= "</attr>";
                } else {
                    $xml .= "</{$key}>";
                }
            } else {
                if (is_int($key)) {
                    $xml .= "<attr id=\"{$key}\"><![CDATA[{$item}]]></attr>\n";
                } else {
                    $xml .= "<{$key}><![CDATA[{$item}]]></{$key}>\n";
                }
            }
        }

        return $xml;
    }
}
