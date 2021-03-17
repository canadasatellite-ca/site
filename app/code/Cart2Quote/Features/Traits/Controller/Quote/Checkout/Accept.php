<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Quote\Checkout;
/**
 * Trait Accept
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
trait Accept
{
    /**
     * Redirect to customer checkout page if the quotation customer is the same customer as logged in
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function execute()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->initQuote();
        if ($this->isSameCustomer()) {
            return $this->proceedToCheckout();
        }
        return $this->defaultRedirect();
		}
	}
}
