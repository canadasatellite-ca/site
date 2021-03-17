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
class AdvancedCSV extends \Aitoc\OrdersExportImport\Model\Converter\CSV
{
    /**
     * @param $collection
     * @return array
     */
    public function appendCollection($collection)
    {
        $csv = [];
        $this->groupHeaders($collection);
        $csv[] = $keys = $this->getHeaders();
        foreach ($this->partCollection($collection) as $key => $element) {
            $this->unsetColumn();
            $this->setKeys($keys);
            $this->setColumns(['entity_type' => 'order']);
            $this->addColumns($this->unsetForeignes($element->toArray()));
            $csv[] = $this->getColumns();
            $this->unsetColumn();
            foreach ($this->getEntities($element) as $label => $collect) {
                if (count($collect)) {
                    if (!isset($collect['inner'])) {
                        foreach ($collect as $code => $lines) {
                            if (count($lines['collection'])) {
                                foreach ($lines['collection'] as $key => $item) {
                                    $this->setKeys($keys);
                                    $this->setColumns(['entity_type' => $lines['item']]);
                                    $this->addColumns($item->toArray());
                                    $csv[] = $this->getColumns();
                                    $this->unsetColumn();
                                }
                            }
                        }
                    } else {
                        foreach ($collect['elements'] as $item) {
                            $array = $item['element']->toArray();
                            $this->setKeys($keys);
                            $this->setColumns(['entity_type' => $collect['item']]);
                            $this->addColumns($array);
                            $csv[] = $this->getColumns();
                            $this->unsetColumn();
                            foreach ($item['inner'] as $method => $element) {
                                foreach ($element['collection'] as $col) {
                                    $array = $col->toArray();
                                    $this->setKeys($keys);
                                    $this->setColumns(
                                        ['entity_type' => $collect['item'] . self::COLON . $element['item']]
                                    );
                                    $this->addColumns($array);
                                    $csv[] = $this->getColumns();
                                    $this->unsetColumn();
                                }
                            }
                        }
                    }
                }
            }
        }

        return $csv;
    }

    /**
     * @param $collection
     */
    public function groupHeaders($collection)
    {

        $this->setHeaders(['entity_type']);
        $this->setHeaders($this->getHeadersFields($collection->getFirstItem()->toArray()));
        foreach ($this->getEntities($collection, 1) as $label => $collect) {
            if (count($collect)) {
                if (!isset($collect['inner'])) {
                    foreach ($collect as $code => $lines) {
                        $this->setHeaders($lines['keys']);
                    }
                } else {
                    if (isset($collect['elements'])) {
                        foreach ($collect['elements'] as $item) {
                            $this->setHeaders($item['element']);
                            foreach ($item['inner'] as $method => $element) {
                                for ($i = 1; $i <= $element['count']; $i++) {
                                    $this->setHeaders($element['keys']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $keys
     */
    public function setHeaders($keys)
    {
        if ($this->headerColumns == null) {
            $this->headerColumns = [];
        }
        $this->headerColumns = array_merge($this->headerColumns, array_diff($keys, $this->headerColumns));
    }

    /**
     * Get keys from fields
     * @param $item
     * @return array
     */
    public function getHeadersFields($item)
    {
        $keys = [];
        foreach ($item as $key => $value) {
            $keys[] = $key;
        }

        return $keys;
    }

    /**
     * Add columns
     *
     * @param $columns
     */
    public function addColumns($columns)
    {
        foreach ($columns as $key => $value) {
            $this->columns[$key] = $value;
        }
    }
}
