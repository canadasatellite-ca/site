<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Variant
 */
class Variant extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_listing_variant',
            'id'
        );
    }

    /**
     * Inserts/updates listing variants
     *
     * @param array $data
     * @return void
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
