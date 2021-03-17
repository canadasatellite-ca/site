<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Multiple
 */
class Multiple extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_listing_multiple',
            'id'
        );
    }

    /**
     * Inserts/updates listing multiple matches
     *
     * @param array $rows
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insert(array $rows)
    {
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();

        foreach ($rows as $row) {
            $connection->insertOnDuplicate($tableName, $row, []);
        }
    }
}
