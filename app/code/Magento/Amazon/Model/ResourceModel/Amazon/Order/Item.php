<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Order;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Item
 */
class Item extends AbstractDb
{
    const CHUNK_SIZE = '1000';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_order_item',
            'id'
        );
    }

    /**
     * Inserts Amazon order items
     *
     * @param array $data
     * @return void
     */
    public function insert(array $data)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        try {
            $connection->insertOnDuplicate($tableName, $data, []);
        } catch (\Exception $e) {
            // todo - add exception logging
        }
    }
}
