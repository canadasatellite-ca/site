<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\Casat\Plugin\Framework\View\Element\UiComponent\DataProvider;

use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Api\Filter;

/**
 * Class Fulltext
 */
class FulltextFilter
{
    public function beforeApply($subject, $collection, $filter)
    {
        if (get_class($collection) == 'Magento\Sales\Model\ResourceModel\Order\Grid\Collection') {
            $v = $filter->getValue();
            $vv = explode(' ',$v);
            $vvv = array();
            foreach($vv as $tmp){
                if(!$tmp){
                    continue;
                }
                $vvv[] = '+'.$tmp;
            }
            $v = '"'.implode(' ',$vvv).'"';
            $filter->setValue(trim($v));
            return array($collection, $filter);
        }
    }
    public function aroundApply($subject, \Closure $process, $collection, $filter){
        if (get_class($collection) == 'Magento\Sales\Model\ResourceModel\Order\Grid\Collection') {
            if (!$collection instanceof AbstractDb) {
                throw new \InvalidArgumentException('Database collection required.');
            }

            /** @var SearchResult $collection */
            $mainTable = $collection->getMainTable();
            $columns = $this->getFulltextIndexColumns($collection, $mainTable);
            if (!$columns) {
                return;
            }

            $columns = $this->addTableAliasToColumns($columns, $collection, $mainTable);
            /** @var \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection */
            $collection->getSelect()
                ->where(
                    'MATCH(' . implode(',', $columns) . ') AGAINST(? IN BOOLEAN MODE)',
                    $filter->getValue()
                );
        } else {
            return $process($collection, $filter);
        }
    }
    protected function getFulltextIndexColumns(AbstractDb $collection, $indexTable)
    {
        $indexes = $collection->getConnection()->getIndexList($indexTable);
        foreach ($indexes as $index) {
            if (strtoupper($index['INDEX_TYPE']) == 'FULLTEXT') {
                return $index['COLUMNS_LIST'];
            }
        }
        return [];
    }

    /**
     * Add table alias to columns
     *
     * @param array $columns
     * @param AbstractDb $collection
     * @param string $indexTable
     * @return array
     */
    protected function addTableAliasToColumns(array $columns, AbstractDb $collection, $indexTable)
    {
        $alias = '';
        foreach ($collection->getSelect()->getPart('from') as $tableAlias => $data) {
            if ($indexTable == $data['tableName']) {
                $alias = $tableAlias;
                break;
            }
        }
        if ($alias) {
            $columns = array_map(
                function ($column) use ($alias) {
                    return '`' . $alias . '`.' . $column;
                },
                $columns
            );
        }

        return $columns;
    }
}
