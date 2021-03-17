<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Report\QuoteReport;
/**
 * Trait Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport
 */
trait Collection
{
    /**
     * Return ordered filed
     *
     * @return string
     */
    private function getQuotedField()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return 'quotes_count';
		}
	}
    /**
     * Retrieve selected columns
     *
     * @return array
     */
    private function _getSelectedColumns()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$connection = $this->getConnection();
        if ('month' == $this->_period) {
            $this->periodFormat = $connection->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->periodFormat = $connection->getDateExtractSql(
                'period',
                \Magento\Framework\DB\Adapter\AdapterInterface::INTERVAL_YEAR
            );
        } else {
            $this->periodFormat = $connection->getDateFormatSql('period', '%Y-%m-%d');
        }
        if (!$this->selectedColumns) {
            if ($this->isTotals()) {
                $this->selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->selectedColumns = [
                    'period' => $this->periodFormat,
                    'quotes_count' => 'SUM(quotes_count)',
                    'total_item_qty_quoted' => 'SUM(total_item_qty_quoted)',
                    'total_quoted_amount' => 'SUM(total_quoted_amount)',
                    'total_tax_amount' => 'SUM(total_tax_amount)',
                    'total_shipping_amount' => 'SUM(total_shipping_amount)',
                    'total_qty_quoted' => 'SUM(total_qty_quoted)',
                    'total_qty_proposal' => 'SUM(total_qty_proposal)',
                    'total_qty_ordered' => 'SUM(total_qty_ordered)',
                    'total_qty_canceled' => 'SUM(total_qty_canceled)',
                ];
            }
        }
        return $this->selectedColumns;
		}
	}
    /**
     * Get SQL for get record count
     *
     * @return \Magento\Framework\DB\Select
     */
    private function getSelectCountSql()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_renderFilters();
        $select = clone $this->getSelect();
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        return $this->getConnection()->select()->from($select, 'COUNT(*)');
		}
	}
    /**
     * @return \Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _beforeLoad()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        if (!$this->isTotals()) {
            $this->getSelect()->group($this->periodFormat);
        }
        return parent::_beforeLoad();
		}
	}
    /**
     * Apply quote status filter
     *
     * @return $this
     */
    private function _applyOrderStatusFilter()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->_orderStatus === null) {
            return $this;
        }
        $quoteStatus = $this->_orderStatus;
        if (!is_array($quoteStatus)) {
            $quoteStatus = [$quoteStatus];
        }
        $this->getSelect()->where('quote_status IN(?)', $quoteStatus);
        return $this;
		}
	}
}