<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Address\Total;
/**
 * Trait OriginalSubtotal
 *
 * @package Cart2Quote\Quotation\Model\Quote\Address\Total
 */
trait OriginalSubtotal
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
        return [
            'code' => $this->getCode(),
            'title' => __('Original Subtotal'),
            'value' => $quote->getOriginalSubtotal(),
            'value_incl_tax' => $quote->getOriginalSubtotalInclTax()
        ];
		}
	}
}
