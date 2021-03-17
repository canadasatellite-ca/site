<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Order
 */
class Order extends AbstractDb
{
    const CHUNK_SIZE = '1000';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_order',
            'id'
        );
    }

    /**
     * Inserts Amazon orders
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

        $connection->insertOnDuplicate($tableName, $data, []);
    }

    /**
     * Produces sales revenue by day
     *
     * Used for Amazon Sales Channel dashboard data
     *
     * @param int $merchantId
     * @return array
     */
    public function getRevenueByMerchantId($merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $mainTable = $this->getMainTable();

        $select = $connection->select()->from(
            ['order' => $mainTable],
            []
        )->where(
            'order.status != ?',
            Definitions::CANCELED_ORDER_STATUS
        )->where(
            'order.purchase_date BETWEEN UTC_TIMESTAMP() - INTERVAL 30 DAY AND UTC_TIMESTAMP()'
        )->where(
            'order.merchant_id = ?',
            $merchantId
        )->columns(
            [
                'revenue' => new \Zend_Db_Expr('SUM(order.total)'),
                'order_age' => new \Zend_Db_Expr('ABS(TIMESTAMPDIFF(DAY, UTC_TIMESTAMP(), order.purchase_date))')
            ]
        )->group('order_age')->order(['order_age ' . Select::SQL_ASC]);

        return $connection->fetchAll($select);
    }
}
