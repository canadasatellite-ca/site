<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Total;
/**
 * Trait Tax
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
trait Tax
{
    /**
     * Get tax amount for display on PDF
     *
     * @return array
     */
    private function getTotalsForDisplay()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        $totals = [];
        $store = $this->getSource()->getStore();
        if ($this->_taxConfig->displaySalesTaxWithGrandTotal($store)) {
            return [];
        }
        $tax = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $tax = $tax + $item->getTaxAmount();
        }
        //add shipping tax
        if ($quote->getShippingAddress()->getShippingTaxAmount()) {
            $tax = $tax + $quote->getShippingAddress()->getShippingTaxAmount();
        }
        if ($this->_taxConfig->displaySalesFullSummary($store)) {
            $totals = $this->getFullTaxInfo();
        }
        $tax = $quote->formatPriceTxt($tax);
        $totals = array_merge($totals, parent::getTotalsForDisplay());
        $totals[0]['amount'] = $tax;
        return $totals;
		}
	}
}
