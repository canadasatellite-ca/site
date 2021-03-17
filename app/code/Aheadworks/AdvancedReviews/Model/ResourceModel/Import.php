<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType;
use Magento\Framework\DB\Select;

/**
 * Class Import
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel
 */
class Import extends AbstractDb
{
    /**#@+
     * Constant defined for manipulate import process.
     */
    const LIMIT = 100;
    const NATIVE_REVIEW_ID = 'review_id';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Review::MAIN_TABLE_NAME, Review::MAIN_TABLE_ID_FIELD_NAME);
    }

    /**
     * Get existing review data
     *
     * @param int $lastImportedId
     * @return array
     */
    public function getExistingReviewsData($lastImportedId)
    {
        $connection = $this->getConnection();
        $nativeReviewsSelect = $this->getNativeReviewSelect($lastImportedId);
        $nativeReviewsSelect->limit(self::LIMIT);

        return $connection->fetchAll($nativeReviewsSelect);
    }

    /**
     * Get native review select
     *
     * @param int $lastImportedId
     * @return Select
     */
    private function getNativeReviewSelect($lastImportedId)
    {
        $select = $this->getConnection()
            ->select()
            ->from(
                [ 'rt' => $this->getTable('review')],
                [
                    self::NATIVE_REVIEW_ID => 'rt.review_id',
                    ReviewInterface::CREATED_AT => 'rt.created_at',
                    ReviewInterface::PRODUCT_ID => 'rt.entity_pk_value',
                    ReviewInterface::STATUS => 'rt.status_id'
                ]
            )->join(
                ['rdt' => $this->getTable('review_detail')],
                'rdt.review_id = rt.review_id',
                [
                    ReviewInterface::SUMMARY => 'rdt.title',
                    ReviewInterface::NICKNAME => 'rdt.nickname',
                    ReviewInterface::CONTENT => 'rdt.detail',
                    ReviewInterface::CUSTOMER_ID => 'rdt.customer_id',
                    ReviewInterface::STORE_ID => 'rdt.store_id'
                ]
            )->join(
                ['rst' => new \Zend_Db_Expr('(' . $this->getSharedStoresSelect() . ')')],
                'rst.review_id = rt.review_id',
                [
                    ReviewInterface::SHARED_STORE_IDS => 'rst.' . ReviewInterface::SHARED_STORE_IDS
                ]
            )->joinLeft(
                ['rr' => new \Zend_Db_Expr('(' . $this->getRatingSelect() . ')')],
                'rr.review_id = rt.review_id',
                [
                    ReviewInterface::RATING => 'IFNULL(rr.'. ReviewInterface::RATING . ', 0)'
                ]
            )->where('rt.review_id > ' . $lastImportedId);

        return $select;
    }

    /**
     * Get shared stores select
     *
     * @return Select
     */
    private function getSharedStoresSelect()
    {
        $select = $this->getConnection()
            ->select()
            ->from(
                ['tmp' => $this->getTable('review_store')],
                [
                    self::NATIVE_REVIEW_ID => 'tmp.review_id',
                    ReviewInterface::SHARED_STORE_IDS => new \Zend_Db_Expr('GROUP_CONCAT(tmp.store_id)')
                ]
            )->group('tmp.review_id');

        return $select;
    }

    /**
     * Get rating select
     *
     * @return Select
     */
    private function getRatingSelect()
    {
        $connection = $this->getConnection();

        $sumVotesPercentColumn = 'SUM(tmp.percent)';
        $votesCountColumn = 'COUNT(*)';
        $select = $connection->select()
            ->from(
                ['tmp' => $this->getTable('rating_option_vote')],
                [
                    self::NATIVE_REVIEW_ID => 'tmp.review_id',
                    ReviewInterface::RATING => new \Zend_Db_Expr($sumVotesPercentColumn . '/' . $votesCountColumn)
                ]
            )->group('tmp.review_id');

        return $select;
    }
}
