<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;

/**
 * Class DashboardCollection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport
 */
class DashboardCollection extends \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Report\QuoteReport\DashboardCollection {
        getDateRange as private traitGetDateRange;
        prepareSummary as private traitPrepareSummary;
        _prepareSummaryLive as private _traitPrepareSummaryLive;
        _getTZRangeOffsetExpression as private _traitGetTZRangeOffsetExpression;
        _getRangeExpression as private _traitGetRangeExpression;
        isLive as private traitIsLive;
    }

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Reports quote factory
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReportFactory
     */
    protected $_reportQuoteFactory;

    /**
     * DashboardCollection constructor
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Snapshot $entitySnapshot
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReportFactory $reportQuoteFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Snapshot $entitySnapshot,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReportFactory $reportQuoteFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_reportQuoteFactory = $reportQuoteFactory;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
    }

    /**
     * Calculate From and To dates (or times) by given period
     *
     * @param string $range
     * @param string $customStart
     * @param string $customEnd
     * @param bool $returnObjects
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false)
    {
        return $this->traitGetDateRange($range, $customStart, $customEnd, $returnObjects);
    }

    /**
     * Prepare report summary
     *
     * @param string $range
     * @param string $customStart
     * @param string $customEnd
     * @param int $isFilter
     * @return $this
     */
    public function prepareSummary($range, $customStart, $customEnd, $isFilter = 0)
    {
        return $this->traitPrepareSummary($range, $customStart, $customEnd, $isFilter);
    }

    /**
     * Prepare report summary from live data
     *
     * @param string $range
     * @param string $customStart
     * @param string $customEnd
     * @param int $isFilter
     * @return $this
     */
    protected function _prepareSummaryLive($range, $customStart, $customEnd, $isFilter = 0)
    {
        return $this->_traitPrepareSummaryLive($range, $customStart, $customEnd, $isFilter);
    }

    /**
     * Retrieve query for attribute with timezone conversion
     *
     * @param string $range
     * @param string $attribute
     * @param string|null $from
     * @param string|null $to
     * @return string
     */
    protected function _getTZRangeOffsetExpression($range, $attribute, $from = null, $to = null)
    {
        return $this->_traitGetTZRangeOffsetExpression($range, $attribute, $from, $to);
    }

    /**
     * Get range expression
     *
     * @param string $range
     * @return \Zend_Db_Expr
     */
    protected function _getRangeExpression($range)
    {
        return $this->_traitGetRangeExpression($range);
    }

    /**
     * Retrieve is live flag for rep
     *
     * @return bool
     */
    public function isLive()
    {
        return $this->traitIsLive();
    }
}
