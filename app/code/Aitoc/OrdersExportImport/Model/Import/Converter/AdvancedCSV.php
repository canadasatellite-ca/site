<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Import\Converter;

/**
 * Class AdvancedCSV
 * @package Aitoc\OrdersExportImport\Model\Import\Converter
 */
class AdvancedCSV extends \Aitoc\OrdersExportImport\Model\Import\Converter
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
        $params     = $this->getParams();
        $scopeArray = [];
        $list       = explode(";", $filename);
        foreach ($list as $file) {
            $this->publish->initCSV($file, "r");
            $line          = 0;
            $this->headers = [];
            while (($row = $this->publish->readRow()) !== false) {
                if (!$line) {
                    $this->headers = $row;
                } else {
                    if ($row[0] == 'order' && $line > 1) {
                        if (count($scopeArray) > 0) {
                            $this->appendCsv($scopeArray);
                            $scopeArray   = [];
                            $scopeArray[] = $row;
                        }
                    } else {
                        $scopeArray[] = $row;
                    }
                }
                $line++;
            }
            if (count($scopeArray) > 0) {
                $this->appendCsv($scopeArray);
            }
        }
    }

    /**
     * @param $csv
     */
    public function appendCsv($csv)
    {
        $modelArray = [];
        if (count($csv)) {
            $modelArray = $this->getNodes($csv);
            $this->setModeles($modelArray);
        }
    }

    /**
     * @param $csv
     * @return array
     */
    public function getNodes($csv)
    {
        $array  = [];
        $counts = [];
        foreach ($csv as $key => $element) {
            $newArray = [];
            foreach ($element as $ki => $record) {
                if ($record && $ki < count($this->headers)) {
                    $newArray[$this->headers[$ki]] = $this->onSerialize($this->isDate($record));
                }
            }
            $entity = $newArray['entity_type'];
            unset($newArray['entity_type']);
            if ($entity == 'order') {
                $array['order']['fields'] = $newArray;
                continue;
            }

            $entities = explode(":", $entity);
            if (count($entities) > 1) {
                $result      = $this->searchEntity($entities[0], 0);
                $resultInner = $this->searchEntity($entities[1], $result);
                $array['order'][$result][$counts[$result]['counts']][$resultInner][] = $newArray;
            } else {
                $result = $this->searchEntity($entity, 0);
                if (!isset($counts[$result]['counts'])) {
                    $counts[$result]['counts'] = 0;
                } else {
                    $counts[$result]['counts']++;
                }
                if (in_array($result, ['invoices', 'shipments', 'creditmemos'])) {
                    $array['order'][$result][$counts[$result]['counts']]['fields'] = $newArray;
                } else {
                    $array['order'][$result][$counts[$result]['counts']] = $newArray;
                }
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
}
