<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Quote\Checkout;
/**
 * Trait Customer
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
trait Customer
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
        if ($this->isAutoLogin() && $this->hasValidHash()) {
            $this->autoLogin();
            if ($this->isAutoConfirm()) {
                return $this->proceedToCheckout();
            }
        }
        return $this->defaultRedirect();
		}
	}
}
