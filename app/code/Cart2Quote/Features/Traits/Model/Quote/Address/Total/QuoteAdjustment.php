<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Address\Total;
/**
 * Trait QuoteAdjustment
 *
 * @package Cart2Quote\Quotation\Model\Quote\Address\Total
 */
trait QuoteAdjustment
{
    /**
     * Assign quote adjustment amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    private function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            return [];
        }
        $value = ((double)$total->getSubtotal()) - (double)$quote->getOriginalSubtotal();
        $valueInclTax = min(0, ((double)$total->getSubtotalInclTax()) - (double)$quote->getOriginalSubtotalInclTax());
        //remove WEEE
        if ($this->weeeHelper->includeInSubtotal($quote->getStore())) {
            $value = $value - (double)$total->getWeeeTotalExclTax();
            $valueInclTax = $valueInclTax - (double)$total->getWeeeTotalExclTax() - (double)$total->getWeeeTaxAmount();
        }
        return [
            'code' => $this->getCode(),
            'title' => __('Quote Adjustment'),
            'value' => $value,
            'value_incl_tax' => $valueInclTax
        ];
		}
	}
}
