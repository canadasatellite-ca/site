<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Listing;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Log
 */
class Log extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_listing_log',
            'id'
        );
    }

    /**
     * Inserts/updates listing logs
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insert(array $data)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        $connection->insertOnDuplicate($tableName, $data, []);
    }

    /**
     * Clear logs
     *
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @var int $days
     */
    public function clearLogs($days)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        // verify numeric value
        if (is_numeric($days)) {
            $where = [
                'created_on < DATE_SUB(NOW(), INTERVAL ' . $days . ' DAY)'
            ];

            try {
                $connection->beginTransaction();
                $connection->delete($tableName, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }
}
