<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Import\Converter;

/**
 * Class XML
 * @package Aitoc\OrdersExportImport\Model\Import\Converter
 */
class XML extends \Aitoc\OrdersExportImport\Model\Import\Converter
{
    /**
     * @param $filename
     */
    public function toDB($filename)
    {
        $list = explode(";", $filename);
        foreach ($list as $file) {
            $xml = $this->publish->readXml($file);
            $this->appendXml($xml);
        }
    }

    /**
     * @param $xml
     */
    public function appendXml($xml)
    {
        $modelArray = [];
        if (count($xml)) {
            $modelArray = $this->getNodes($xml, $this->entities->getImportEntities(), 0);
        }
        foreach ($this->partCollection($modelArray) as $val) {
            $this->setModeles($val);
        }
    }

    /**
     * @param $collection
     * @param $entities
     * @param $level
     * @return array
     */
    public function getNodes($collection, $entities, $level)
    {
        $array = [];
        if (!$level) {
            $node = $collection->getElementsByTagName('order');
            foreach ($node as $child) {
                $array[]['order'] = $this->getNodes($child, $entities['order'], $level + 1);
            }
        } else {
            foreach ($collection->childNodes as $element) {
                $node = $element->nodeName;
                $keys = array_keys($entities);
                if (in_array($node, $keys)) {
                    $array[$node] = $this->getNode($element, $entities[$element->nodeName]);
                }
            }
        }

        return $array;
    }

    /**
     * @param $collection
     * @param $entities
     * @return array
     */
    public function getNode($collection, $entities)
    {
        $params = $this->getParams();
        $array  = [];
        if (!is_array($entities)) {
            foreach ($collection->childNodes as $element) {
                if ($element->nodeName != '#text') {
                    $array[$element->nodeName] = $element->nodeValue;
                    if ($element->nodeName == 'product_options') {
                        $array[$element->nodeName] = '';
                    }
                }
            }
        } else {
            if (count($entities) == 1) {
                foreach ($collection->childNodes as $element) {
                    $array[] = $this->getNode($element, $entities['item']);
                }
            } else {
                unset($entities['item']);
                if (!$params['oldmagento']) {
                    foreach ($collection->childNodes as $col => $element) {
                        foreach ($element->childNodes as $child) {
                            $array[$col][$child->nodeName] = $this->getNode($child, $entities[$child->nodeName]);
                        }
                    }
                } else {
                    foreach ($collection->childNodes as $col => $element) {
                        foreach ($element->childNodes as $child) {
                            if (!in_array($child->nodeName, ['items', 'comments', 'trackingsinformation'])) {
                                $array[$col]['fields'][$child->nodeName] = $child->nodeValue;
                            } else {
                                $array[$col][$child->nodeName] = $this->getNode($child, $entities[$child->nodeName]);
                            }
                        }
                    }
                }
            }
        }

        return $array;
    }
}
