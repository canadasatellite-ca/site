<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Status\History;
use Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection\AbstractCollection;
use Cart2Quote\Quotation\Api\Data\QuoteStatusHistorySearchResultInterface;
/**
 * Flat quotation quote status history collection
 */
trait Collection
{
    /**
     * Get history object collection for specified instance (quote, shipment, invoice or credit memo)
     * - Parameter instance may be one of the following types: \Cart2Quote\Quotation\Model\Quote
     *
     * @param \Magento\Sales\Model\AbstractModel $instance
     * @return \Cart2Quote\Quotation\Model\Quote\Status\History|null
     */
    private function getUnnotifiedForInstance($instance)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$instance instanceof \Cart2Quote\Quotation\Model\Quote) {
            $instance = $instance->getQuote();
        }
        $this->setQuoteFilter(
            $instance
        )->setOrder(
            'created_at',
            'desc'
        )->addFieldToFilter(
            'entity_name',
            $instance->getEntityType()
        )->addFieldToFilter(
            'is_customer_notified',
            0
        )->setPageSize(
            1
        );
        foreach ($this->getItems() as $historyItem) {
            return $historyItem;
        }
        return null;
		}
	}
    /**
     * Model initialization
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(
            \Cart2Quote\Quotation\Model\Quote\Status\History::class,
            \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History::class
        );
		}
	}
}
