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
class CSV extends \Aitoc\OrdersExportImport\Model\Converter
{

    const COLON = ':';

    /**
     * @var array
     */
    public $headerColumns;

    /**
     * @var array
     */
    public $columns;

    /**
     * Source file handler.
     *
     * @var \Magento\Framework\Filesystem\File\Write
     */
    private $fileHandler;

    /**
     * @param $filename
     * @return array
     */
    public function toFile($filename)
    {
        $config     = $this->getConfig();
        $collection = $this->filter();
        $csv        = $this->appendCollection($collection);
        if (!$config['path_local']) {
            $config['path_local'] = 'var/tmp/';
        }
        $this->publish->setPath($config['path_local']);
        $this->publish->initCSV($filename);
        foreach ($csv as $elements) {
            $this->publish->writeRow($elements, $config['delimeter'], $config['enclose']);
        }
        $this->publish->destruct();

        return [$this->publish->getPathFile(), $this->publish->getFilename()];
    }

    /**
     * @param $collection
     * @return array
     */
    public function appendCollection($collection)
    {
        $csv     = [];
        $counts  = round($collection->getSize() / 90, 1);
        $percent = 10;
        $this->groupHeaders($collection);
        $csv[0] = $keys = $this->getHeaders();
        foreach ($this->partCollection($collection) as $key => $element) {
            $this->unsetColumn();
            $this->setKeys($keys);
            $this->setColumns($this->unsetForeignes($element->toArray()));
            foreach ($this->getEntities($element) as $label => $collect) {
                if (count($collect)) {
                    if (!isset($collect['inner'])) {
                        foreach ($collect as $code => $lines) {
                            if (count($lines['collection'])) {
                                $inc = 1;
                                foreach ($lines['collection'] as $key => $item) {
                                    $this->setColumns($this->createNewEntity($item->toArray(), $lines['item'] . $inc));
                                    $inc++;
                                }
                            }
                        }
                    } else {
                        $inc = 1;
                        foreach ($collect['elements'] as $item) {
                            $array = $item['element']->toArray();
                            $this->setColumns($this->createNewEntity($array, $collect['item'] . $inc));
                            $incTo = 1;
                            foreach ($item['inner'] as $method => $element) {
                                foreach ($element['collection'] as $col) {
                                    $array = $col->toArray();
                                    $this->setColumns($this->createNewEntity($array, $collect['item'] . $inc . self::COLON . $element['item'] . $incTo));
                                    $incTo++;
                                }
                            }
                            $inc++;
                        }
                    }
                }
            }
            $csv[] = $this->getColumns();
            $percent += $counts;
            $stack = $this->getStack();
            $stack->setPercent($percent);
            $stack->save();
        }

        return $csv;
    }

    /**
     * @param $collection
     */
    public function groupHeaders($collection)
    {
        $this->setHeaders($this->getHeadersFields($collection->getFirstItem()->toArray()));
        foreach ($this->getEntities($collection, 1) as $label => $collect) {
            if (count($collect)) {
                if (!isset($collect['inner'])) {
                    foreach ($collect as $code => $lines) {
                        $keys = [];
                        for ($i = 1; $i <= $lines['count']; $i++) {
                            foreach ($lines['keys'] as $key) {
                                $keys[] = $lines['item'] . $i . self::COLON . $key;
                            }
                        }
                        $this->setHeaders($keys);
                    }
                } else {
                    $inc = 1;
                    if (isset($collect['elements'])) {
                        foreach ($collect['elements'] as $item) {
                            $keys = [];
                            foreach ($item['element'] as $key) {
                                $keys[] = $collect['item'] . $inc . self::COLON . $key;
                            }
                            $this->setHeaders($keys);
                            $keys   = [];
                            $incTo  = 1;
                            $keys[] = $collect['item'] . $i . self::COLON . $key;
                            foreach ($item['inner'] as $method => $element) {
                                for ($i = 1; $i <= $element['count']; $i++) {
                                    foreach ($element['keys'] as $key) {
                                        $keys[] = $collect['item']
                                            . $i . self::COLON . $element['item'] . $i . self::COLON . $key;
                                    }
                                }
                                $this->setHeaders($keys);
                            }
                        }
                    }
                }
            }
        }
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
     * Set keys
     *
     * @param $keys
     */
    public function setKeys($keys)
    {
        $arr = [];
        foreach ($keys as $key) {
            $arr[$key] = '';
        }

        $this->setColumns($arr);
    }

    /**
     * Set keys on headerColumns
     *
     * @param $keys
     */
    public function setHeaders($keys)
    {
        if ($this->headerColumns == null) {
            $this->headerColumns = [];
        }
        $this->headerColumns = array_merge($this->headerColumns, $keys);
    }

    /**
     * Get keys
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headerColumns;
    }

    /**
     * Unset columns
     *
     */
    public function unsetColumn()
    {
        $this->columns = [];
    }

    /**
     * Set columns
     *
     * @param $columns
     */
    public function setColumns($columns)
    {

        if ($this->columns == null) {
            $this->columns = [];
        }
        $this->columns = array_merge($this->columns, $columns);
    }

    /**
     * Get columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Add Entity
     *
     * @param $array
     * @param $name
     * @return array
     */
    public function createNewEntity($array, $name)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$name . self::COLON . $key] = $value;
        }

        return $newArray;
    }

    public function unsetForeignes($array)
    {
        $newArray = $array;
        foreach ($newArray as $key => $value) {
            if (is_array($value) || is_object($value)) {
                unset($newArray[$key]);
            }
        }

        return $newArray;
    }
}
