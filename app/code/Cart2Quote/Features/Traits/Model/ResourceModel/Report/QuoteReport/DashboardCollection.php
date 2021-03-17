<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Report\QuoteReport;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
/**
 * Trait DashboardCollection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport
 */
trait DashboardCollection
{
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
    private function getDateRange($range, $customStart, $customEnd, $returnObjects = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$dateEnd = new \DateTime();
        $dateStart = new \DateTime();
        // go to the end of a day
        $dateEnd->setTime(23, 59, 59);
        $dateStart->setTime(0, 0, 0);
        switch ($range) {
            case '24h':
                $dateEnd = new \DateTime();
                $dateEnd->modify('+1 hour');
                $dateStart = clone $dateEnd;
                $dateStart->modify('-1 day');
                break;
            case '7d':
                // substract 6 days we need to include
                // only today and not hte last one from range
                $dateStart->modify('-6 days');
                break;
            case '1m':
                $dateStart->setDate(
                    $dateStart->format('Y'),
                    $dateStart->format('m'),
                    $this->_scopeConfig->getValue(
                        'reports/dashboard/mtd_start',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                );
                break;
            case 'custom':
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd = $customEnd ? $customEnd : $dateEnd;
                break;
            case '1y':
            case '2y':
                $startMonthDay = explode(
                    ',',
                    $this->_scopeConfig->getValue(
                        'reports/dashboard/ytd_start',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                );
                $startMonth = isset($startMonthDay[0]) ? (int)$startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int)$startMonthDay[1] : 1;
                $dateStart->setDate($dateStart->format('Y'), $startMonth, $startDay);
                if ($range == '2y') {
                    $dateStart->modify('-1 year');
                }
                break;
        }
        if ($returnObjects) {
            return [$dateStart, $dateEnd];
        } else {
            return ['from' => $dateStart, 'to' => $dateEnd, 'datetime' => true];
        }
		}
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
    private function prepareSummary($range, $customStart, $customEnd, $isFilter = 0)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_prepareSummaryLive($range, $customStart, $customEnd, $isFilter);
        return $this;
		}
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
    private function _prepareSummaryLive($range, $customStart, $customEnd, $isFilter = 0)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$table = 'quotation_quote';
        $this->setMainTable($table);
        $from = [];
        $from['main_table'] = [];
        $from['main_table']['tableName'] = $this->getMainTable();
        $from['main_table']['schema'] = null;
        $from['main_table']['joinType'] = \Magento\Framework\DB\Select::FROM;
        $this->getSelect()->setPart(\Magento\Framework\DB\Select::FROM, $from);
        /**
         * Reset all columns, because result will group only by 'created_at' field
         */
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        $dateRange = $this->getDateRange($range, $customStart, $customEnd);
        $tzRangeOffsetExpression = $this->_getTZRangeOffsetExpression(
            $range,
            'quotation_created_at',
            $dateRange['from'],
            $dateRange['to']
        );
        $this->getSelect()->columns(
            ['quantity' => 'COUNT(main_table.quote_id)', 'range' => $tzRangeOffsetExpression]
        )->joinLeft(
            $this->getTable('quote'),
            'entity_id=main_table.quote_id',
            'store_id'
        )->where(
            'main_table.state NOT IN (?)',
            [
                \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW,
                \Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED
            ]
        )->where(
            'main_table.state IS NOT NULL'
        )->where(
            'main_table.is_quote = ?',
            1
        )->order(
            'range',
            \Magento\Framework\DB\Select::SQL_ASC
        )->group(
            $tzRangeOffsetExpression
        );
        $this->addFieldToFilter('quotation_created_at', $dateRange);
        return $this;
		}
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
    private function _getTZRangeOffsetExpression($range, $attribute, $from = null, $to = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return str_replace(
            '{{attribute}}',
            $this->_reportQuoteFactory->create()->getStoreTZOffsetQuery($this->getMainTable(), $attribute, $from, $to),
            $this->_getRangeExpression($range)
        );
		}
	}
    /**
     * Get range expression
     *
     * @param string $range
     * @return \Zend_Db_Expr
     */
    private function _getRangeExpression($range)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			switch ($range) {
            case '24h':
                $expression = $this->getConnection()->getConcatSql(
                    [
                        $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d %H:'),
                        $this->getConnection()->quote('00'),
                    ]
                );
                break;
            case '7d':
            case '1m':
                $expression = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d');
                break;
            case '1y':
            case '2y':
            case 'custom':
            default:
                $expression = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m');
                break;
        }
        return $expression;
		}
	}
    /**
     * Retrieve is live flag for rep
     *
     * @return bool
     */
    private function isLive()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return true;
		}
	}
}
