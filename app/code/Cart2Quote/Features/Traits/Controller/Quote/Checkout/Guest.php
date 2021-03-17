<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Quote\Checkout;
/**
 * Trait Guest
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
trait Guest
{
    /**
     * Redirect to customer dashboard or checkout page
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function execute()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->initQuote();
        if ($this->isGuest()) {
            return $this->proceedToCheckout(true);
        }
        return $this->defaultRedirect();
		}
	}
}
