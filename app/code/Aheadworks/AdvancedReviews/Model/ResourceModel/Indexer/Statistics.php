<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Indexer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Indexer\Model\ResourceModel\AbstractResource;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface as IR;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResource;
use Aheadworks\AdvancedReviews\Api\Data\StatisticsInterface as IS;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Indexer\Table\StrategyInterface;

/**
 * Class Statistics
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Indexer
 */
class Statistics extends AbstractResource
{
    /**
     * @var int
     */
    const INSERT_PER_QUERY = 500;

    /**#@+
     * Constants defined for tables
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_ar_statistics';
    const MAIN_TABLE_ID_FIELD_NAME = 'product_id';
    const TMP_TABLE_ALIAS = 'tmp_table';
    /**#@-*/

    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @param Context $context
     * @param StrategyInterface $tableStrategy
     * @param DateTimeFormatter $dateTimeFormatter
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
        DateTimeFormatter $dateTimeFormatter,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * Reindex all reviews data
     *
     * @return $this
     * @throws \Exception
     */
    public function reindexAll()
    {
        $this->clearIndexTable();
        $this->beginTransaction();
        try {
            $toInsert = $this->prepareStatisticsData();
            $this->insertDataToTable($toInsert);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Reindex reviews data for defined ids
     *
     * @param array|int $productIds
     * @return $this
     * @throws \Exception
     */
    public function reindexRows($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }
        $toUpdate = $this->prepareStatisticsData($productIds);
        $this->beginTransaction();
        try {
            $this->removeDataByProductIds($productIds);
            $this->insertDataToTable($toUpdate);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Clear index table
     *
     * @throws LocalizedException
     */
    private function clearIndexTable()
    {
        $this->getConnection()->delete($this->getMainTable());
    }

    /**
     * Remove data by product ids
     *
     * @param array $productIds
     * @throws LocalizedException
     */
    private function removeDataByProductIds($productIds)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [IR::PRODUCT_ID . ' IN (?)' => $productIds]
        );
    }

    /**
     * Prepare and return data for insert to index table
     *
     * @param array|null $productIds
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    private function prepareStatisticsData($productIds = null)
    {
        $selectForStatisticsData = $this->getSelectForStatisticsData();

        if ($productIds) {
            $selectForStatisticsData->where(IR::PRODUCT_ID . ' IN (?)', $productIds);
        }

        return $this->getConnection()->fetchAll($selectForStatisticsData);
    }

    /**
     * Get select for statistics data
     *
     * @return Select
     * @throws \Zend_Db_Select_Exception
     */
    private function getSelectForStatisticsData() {
        $conn = $this->getConnection();
        # 2021-04-30 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# "Aheadworks_AdvancedReviews: reviews should be shown on the frontend regardless the store":
		# https://github.com/canadasatellite-ca/site/issues/81
        $tmp = df_db_from(
        	['r' => df_table('aw_ar_review')]
			,[
				IR::STORE_ID => 's.' . IR::STORE_ID,
				IR::PRODUCT_ID => 'r.' . IR::PRODUCT_ID,
				IR::RATING => 'r.' . IR::RATING
			]
		)->joinCross(['s' => df_table('store')], [])
			->where('r.status IN (?)', Status::getDisplayStatuses())
			->where('r.created_at <= ?', $this->getCurrentFormattedDate())
		;
        return $conn
            ->select()
            ->from(
                [self::TMP_TABLE_ALIAS => $tmp],
                [
                    IS::STORE_ID,
                    IS::PRODUCT_ID,
                    IS::REVIEWS_COUNT => new \Zend_Db_Expr('COUNT(*)'),
                    IS::AGGREGATED_RATING => new \Zend_Db_Expr('CEIL(SUM(' . self::TMP_TABLE_ALIAS . '.rating)/COUNT(*))')
                ]
            )->group([IS::STORE_ID, IS::PRODUCT_ID]);
    }

    /**
     * Get main reviews select
     *
     * @return Select
     */
    private function getMainReviewsSelect()
    {
        $select = $this->getConnection()
            ->select()
            ->from(
                ['review_table' => $this->getTable(ReviewResource::MAIN_TABLE_NAME)],
                [
                    IR::STORE_ID,
                    IR::PRODUCT_ID,
                    IR::RATING
                ]
            )->where(
                'review_table.status IN (?)',
                Status::getDisplayStatuses()
            )->where(
                'review_table.created_at <= ?',
                $this->getCurrentFormattedDate()
            );

        return $select;
    }

    /**
     * Get shared by store reviews select
     *
     * @return Select
     */
    private function getSharedByStoreReviewsSelect()
    {
        $select = $this->getConnection()
            ->select()
            ->from(
                ['review_table' => $this->getTable(ReviewResource::MAIN_TABLE_NAME)],
                [
                    IR::STORE_ID => 'shared_store_table.' . IR::STORE_ID,
                    IR::PRODUCT_ID => 'review_table.' . IR::PRODUCT_ID,
                    IR::RATING => 'review_table.' . IR::RATING
                ]
            )->join(
                ['shared_store_table' => $this->getTable(ReviewResource::SHARED_STORE_TABLE_NAME)],
                'review_table.id = shared_store_table.review_id'
                . ' AND review_table.store_id <> shared_store_table.store_id',
                []
            )->where(
                'review_table.status IN (?)',
                Status::getDisplayStatuses()
            )->where(
                'review_table.created_at <= ?',
                $this->getCurrentFormattedDate()
            );

        return $select;
    }

    /**
     * Retrieve current formatted date
     *
     * @return string
     */
    private function getCurrentFormattedDate()
    {
        return $this->dateTimeFormatter->getDateTimeInDbFormat(null);
    }

    /**
     * Partial insert to index table
     *
     * @param array $data
     * @return $this
     * @throws LocalizedException
     */
    private function insertDataToTable($data)
    {
        $counter = 0;
        $toInsert = [];
        foreach ($data as $row) {
            $counter++;
            $toInsert[] = $row;
            if ($counter % self::INSERT_PER_QUERY == 0) {
                $this->insertToTable($toInsert);
                $toInsert = [];
            }
        }
        $this->insertToTable($toInsert);

        return $this;
    }

    /**
     * Insert to index table
     *
     * @param array $toInsert
     * @return $this
     * @throws LocalizedException
     */
    private function insertToTable($toInsert)
    {
        if (count($toInsert)) {
            $this->getConnection()->insertMultiple(
                $this->getMainTable(),
                $toInsert
            );
        }
        return $this;
    }
}
