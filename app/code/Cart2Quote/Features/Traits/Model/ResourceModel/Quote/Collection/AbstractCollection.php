<?php /** @noinspection ALL */
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Collection;
/**
 * Flat quotation quote collection
 */
trait AbstractCollection
{
    /**
     * Retrieve quotation quote as parent collection object
     *
     * @return \Cart2Quote\Quotation\Model\Quote|null
     */
    private function getQuotationQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_quotationQuote;
		}
	}
    /**
     * Set quotation quote model as parent collection object
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    private function setQuotationQuote($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_quotationQuote = $quote;
        if ($this->_eventPrefix && $this->_eventObject) {
            $this->_eventManager->dispatch(
                $this->_eventPrefix . '_set_quotation_quote',
                ['collection' => $this, $this->_eventObject => $this, 'quote' => $quote]
            );
        }
        return $this;
		}
	}
    /**
     * Add quote filter
     *
     * @param int|\Cart2Quote\Quotation\Model\Quote $quote
     * @return $this
     */
    private function setQuoteFilter($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            $this->setQuotationQuote($quote);
            $quoteId = $quote->getId();
            if ($quoteId) {
                $this->addFieldToFilter($this->_quoteField, $quoteId);
            } else {
                $this->_totalRecords = 0;
                $this->_setIsLoaded(true);
            }
        } else {
            $this->addFieldToFilter($this->_quoteField, $quote);
        }
        return $this;
		}
	}
}
