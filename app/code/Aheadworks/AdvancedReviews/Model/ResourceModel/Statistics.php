<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel;

use Aheadworks\AdvancedReviews\Api\Data\StatisticsInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Statistics
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel
 */
class Statistics extends AbstractDb
{
    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_ar_statistics';
    const MAIN_TABLE_ID_FIELD_NAME = 'product_id';
    /**#@-*/

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function load(AbstractModel $object, $productId = null, $storeId = null)
    {
        $storeId = $this->getPreparedStoreId($storeId);

        $object->setData($this->getDefaultData($productId, $storeId));
        $statisticsData = $productId
            ? $this->getStatisticsData($productId, $storeId)
            : $this->getStatisticsDataForStore($storeId);
        $object->addData($statisticsData);

        return $this;
    }

    /**
     * Retrieve prepared store id
     *
     * @param int|null $storeId
     * @return int
     */
    private function getPreparedStoreId($storeId = null)
    {
        return isset($storeId) ? $storeId : $this->storeManager->getStore()->getId();
    }

    /**
     * Retrieve statistics data for specified product within specific store
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    private function getStatisticsData($productId, $storeId)
    {
        $statisticsData = [];
        try {
            $connection = $this->getConnection();
            if ($connection && !empty($productId) && !empty($storeId)) {
                $select = $connection->select()
                    ->from($this->getMainTable())
                    ->where('product_id = :product_id')
                    ->where('store_id = :store_id');

                $fetchedData = $connection->fetchRow(
                    $select,
                    [
                        'product_id' => $productId,
                        'store_id' => $storeId,
                    ]
                );
                $statisticsData = is_array($fetchedData) ? $fetchedData : [];
            }
        } catch (\Exception $exception) {
            $statisticsData = [];
        }

        return $statisticsData;
    }

    /**
     * Retrieve statistics data for all products within specific store
     *
     * @param int $storeId
     * @return array
     */
    private function getStatisticsDataForStore($storeId)
    {
        $statisticsData = [];
        try {
            $connection = $this->getConnection();
            if ($connection && !empty($storeId)) {
                $subQuery = $connection->select()
                    ->from(
                        $this->getMainTable(),
                        new \Zend_Db_Expr('COUNT(*)')
                    )->where('store_id = :store_id');

                $select = $connection->select()
                    ->from(
                        $this->getMainTable(),
                        [
                            'store_id',
                            'reviews_count' => 'SUM(reviews_count)',
                            'aggregated_rating' => new \Zend_Db_Expr('SUM(aggregated_rating)/(' . $subQuery .')')
                        ]
                    )->where('store_id = :store_id');

                $fetchedData = $connection->fetchRow(
                    $select,
                    [
                        'store_id' => $storeId,
                    ]
                );
                $statisticsData = is_array($fetchedData) ? $fetchedData : [];
            }
        } catch (\Exception $exception) {
            $statisticsData = [];
        }

        return $statisticsData;
    }

    /**
     * Retrieve default statistics data when no statistics is fetched
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    private function getDefaultData($productId, $storeId)
    {
        return [
            StatisticsInterface::PRODUCT_ID => $productId,
            StatisticsInterface::STORE_ID => $storeId,
            StatisticsInterface::AGGREGATED_RATING => 0,
            StatisticsInterface::REVIEWS_COUNT => 0,
        ];
    }
}
