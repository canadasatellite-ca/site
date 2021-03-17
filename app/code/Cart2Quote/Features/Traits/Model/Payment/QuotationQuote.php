<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Payment;
/**
 * Trait QuotationQuote
 *
 * @package Cart2Quote\Quotation\Model\Payment
 */
trait QuotationQuote
{
    /**
     * Is available check for a given quote cart
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    private function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return parent::isAvailable($quote);
		}
	}
}
