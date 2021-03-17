<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Model\ResourceModel\Stack;

/**
 * Class Collection
 *
 * @package Aitoc\OrdersExportImport\Model\ResourceModel\Stack
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'stack_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\OrdersExportImport\Model\Stack', 'Aitoc\OrdersExportImport\Model\ResourceModel\Stack');
    }
}
