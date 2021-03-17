<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Model\ResourceModel\Export;

/**
 * Class Collection
 *
 * @package Aitoc\OrdersExportImport\Model\ResourceModel\Export
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'export_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\OrdersExportImport\Model\Export', 'Aitoc\OrdersExportImport\Model\ResourceModel\Export');
    }
}
