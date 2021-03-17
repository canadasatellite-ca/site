<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ResourceModel\Amazon\Order;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OrderMetrics extends AbstractDb
{
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

    public function getLifetimeSales(array $merchantIds)
    {
        if (!$merchantIds) {
            return [];
        }
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $mainTable = $this->getMainTable();

        $select = $connection->select()->from(
            ['order' => $mainTable],
            []
        )->where(
            'order.status NOT IN (?)',
            [Definitions::CANCELED_ORDER_STATUS, Definitions::ERROR_ORDER_STATUS]
        )->where(
            'order.merchant_id IN (?)',
            $merchantIds
        )->columns(
            [
                'merchant_id' => 'merchant_id',
                'revenue' => new \Zend_Db_Expr('SUM(order.total)'),
            ]
        )->group('merchant_id');

        return $connection->fetchPairs($select);
    }

    public function getRevenue(array $merchantIds, int $periodInDays)
    {
        if (!$merchantIds) {
            return [];
        }
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $mainTable = $this->getMainTable();

        $select = $connection->select()->from(
            ['order' => $mainTable],
            []
        )->where(
            'order.status NOT IN (?)',
            [Definitions::CANCELED_ORDER_STATUS, Definitions::ERROR_ORDER_STATUS]
        )->where(
            "CONVERT_TZ(`purchase_date`, @@session.time_zone, '+00:00') BETWEEN DATE_ADD(UTC_DATE(), INTERVAL ? DAY) AND NOW()",
            -1 * $periodInDays
        )->where(
            'order.merchant_id IN (?)',
            $merchantIds
        )->columns(
            [
                'merchant_id' => 'merchant_id',
                'revenue' => new \Zend_Db_Expr('SUM(order.total)'),
                'date' => new \Zend_Db_Expr('DATE(CONVERT_TZ(`purchase_date`, @@session.time_zone, \'+00:00\'))'),
            ]
        )->group(
            'merchant_id'
        )->group('date');

        return $connection->fetchAll($select);
    }

    /**
     * Produces sales revenue by day
     *
     * Used for Amazon Sales Channel dashboard data
     *
     * @param int $merchantId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
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
            // todo: what about 2nd status with error?
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
