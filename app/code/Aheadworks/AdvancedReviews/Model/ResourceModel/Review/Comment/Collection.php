<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment;

use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment as CommentResource;
use Aheadworks\AdvancedReviews\Model\Review\Comment as CommentModel;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport as AbuseReportResource;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Status as AbuseReportStatus;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Type as AbuseReportType;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment
 */
class Collection extends AbstractCollection
{
    /**
     * Abuse reports count
     */
    const NEW_ABUSE_REPORTS_COUNT = 'new_abuse_reports_count';

    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = ReviewInterface::ID;

    /**
     * @var bool
     */
    private $needToAttachAbuseReportData = false;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CommentModel::class, CommentResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        if ($this->needToAttachAbuseReportData) {
            $this->attachAbuseReportData();
        }
    }

    /**
     * Attach abuse report data
     */
    private function attachAbuseReportData()
    {
        $this->attachRelationTable(
            $this->_getNewAbuseReportsSelect(),
            CommentInterface::ID,
            AbuseReportInterface::ENTITY_ID,
            self::NEW_ABUSE_REPORTS_COUNT,
            self::NEW_ABUSE_REPORTS_COUNT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::addOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function unshiftOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::unshiftOrder($field, $direction);
    }

    /**
     * Join fields for sort order
     *
     * @param string $field
     * @return $this
     * @throws \Exception
     */
    private function joinFieldsForSortOrder($field)
    {
        if ($field == self::NEW_ABUSE_REPORTS_COUNT) {
            $this->joinAbuseReportData();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter(self::NEW_ABUSE_REPORTS_COUNT)) {
            $this->joinAbuseReportData();
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Join abuse reports data
     */
    private function joinAbuseReportData()
    {
        $this->joinLinkageTable(
            $this->_getNewAbuseReportsSelect(),
            CommentInterface::ID,
            AbuseReportInterface::ENTITY_ID,
            self::NEW_ABUSE_REPORTS_COUNT,
            self::NEW_ABUSE_REPORTS_COUNT
        );
    }

    /**
     * Retrieve new abuse reports select
     *
     * @return Select
     */
    private function _getNewAbuseReportsSelect()
    {
        $newAbuseReportsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(*), 0)');
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable(AbuseReportResource::MAIN_TABLE_NAME)],
                [
                    AbuseReportInterface::ENTITY_ID,
                    self::NEW_ABUSE_REPORTS_COUNT => $newAbuseReportsCountExpr
                ]
            )->where(AbuseReportInterface::STATUS . ' = ?', AbuseReportStatus::getDefaultStatus())
            ->where(AbuseReportInterface::ENTITY_TYPE . ' = ?', AbuseReportType::COMMENT)
            ->group(AbuseReportInterface::ENTITY_ID);

        return $this->getConnection()->select()->from(['tmp_table' => $select]);
    }

    /**
     * Set boolean flag for adding corresponding abuse report data
     *
     * @param bool $value
     * @return $this
     */
    public function setNeedToAttachAbuseReportData($value)
    {
        $this->needToAttachAbuseReportData = $value;

        return $this;
    }
}
