<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Import\Converter;

/**
 * Class CSV
 * @package Aitoc\OrdersExportImport\Model\Import\Converter
 */
class CSV extends \Aitoc\OrdersExportImport\Model\Import\Converter
{

    /**
     * @var array
     */
    public $headers;

    /**
     * @var array
     */
    public $entHeaders;

    /**
     * @param $filename
     */
    public function toDB($filename)
    {

        $list = explode(";", $filename);
        foreach ($list as $file) {
            $this->publish->initCSV($file, "r");
            $line          = 0;
            $this->headers = [];
            while (($row = $this->publish->readRow()) !== false) {
                if (!$line) {
                    $this->headers = $row;
                } else {
                    $this->appendCsv($row);
                }
                $line++;
            }
        }
    }

    /**
     * @param $csv
     */
    public function appendCsv($csv)
    {
        $newArray   = [];
        $modelArray = [];
        foreach ($csv as $key => $value) {
            if ($value && $key < count($this->headers)) {
                $newArray[$this->headers[$key]] = $this->onSerialize($this->isDate($value));
            }
        }
        $this->entHeaders = $this->scopeHeaders(array_keys($newArray));
        if (count($newArray)) {
            $modelArray = $this->getNodes($newArray);
            $this->setModeles($modelArray);
        }
    }

    /**
     * @param $row
     * @return array
     */
    public function scopeHeaders($row)
    {
        $scope = [];
        foreach ($row as $key => $element) {
            $entities = explode(":", $element);
            $scope    = array_merge($scope, $this->strPos($entities));
        }

        return $scope;
    }

    /**
     * @param $csv
     * @return array
     */
    public function getNodes($csv)
    {
        $array = [];
        foreach ($csv as $key => $value) {
            $entities = explode(":", $key);
            if (count($entities) > 1) {
                switch (count($entities)) {
                    case 2:
                        $tempArray = $this->pregMatch($entities[0]);
                        if (in_array(
                            $this->entHeaders[$tempArray[1]]['entity'],
                            ['invoices', 'shipments', 'creditmemos']
                        )) {
                            $array['order'][$this->entHeaders[$tempArray[1]]['entity']][$tempArray[2]]['fields'][$entities[1]] = $value;
                        } else {
                            $array['order'][$this->entHeaders[$tempArray[1]]['entity']][$tempArray[2]][$entities[1]] = $value;
                        }
                        break;
                    case 3:
                        $tempArray = $this->pregMatch($entities[0]);
                        $tempArraySecond = $this->pregMatch($entities[1]);
                        $secondEntity = $this->entHeaders[$tempArray[1]]['inner'][$tempArraySecond[1]]['entity'];
                        $array['order'][$this->entHeaders[$tempArray[1]]['entity']][$tempArray[2]][$secondEntity][$tempArraySecond[2]][$entities[2]] = $value;
                        break;
                }
            } else {
                $array['order']['fields'][$key] = $value;
            }
        }

        return $array;
    }

    /**
     * @param $object
     * @param $level
     * @return int|null|string
     */
    private function searchEntity($object, $level)
    {
        $entities = $this->entities->getImportEntities();
        $ent      = null;
        foreach ($entities['order'] as $key => $entity) {
            if (is_array($entity)) {
                foreach ($entity as $keyEl => $element) {
                    if (!$level) {
                        if ($keyEl == 'item' && $element == $object) {
                            $ent = $key;
                        }
                    } else {
                        if (is_array($element)) {
                            foreach ($element as $keyLevel => $elLevel) {
                                if ($keyLevel == 'item' && $elLevel == $object) {
                                    $ent = $keyEl;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ent;
    }

    /**
     * @param $entities
     * @param null $level
     * @return array
     */
    private function strPos($entities, $level = null)
    {
        $array = [];
        if (count($entities) > 1) {
            $entity                      = array_shift($entities);
            $matches                     = $this->pregMatch($entity);
            $array[$matches[1]]          = ['count' => $matches[2],
                'entity' => $this->searchEntity($matches[1], $level)
            ];
            $array[$matches[1]]['inner'] = $this->strPos($entities, $matches[1]);
        }

        return $array;
    }
}
