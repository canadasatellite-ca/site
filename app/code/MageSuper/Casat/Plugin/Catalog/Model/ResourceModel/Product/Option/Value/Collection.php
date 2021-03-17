<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Plugin\Catalog\Model\ResourceModel\Product\Option\Value;

/**
 * Catalog product option values collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection
{
    public function addRowidToResult()
    {
        $optionTitleTable = $this->getTable('optiondependent_value');

        $this->getSelect()->joinLeft(
            ['value_rowid' => $optionTitleTable],
            'value_rowid.option_type_id = main_table.option_type_id',
            ['row_id' => 'row_id','children'=>'children']
        );

        return $this;
    }
}
