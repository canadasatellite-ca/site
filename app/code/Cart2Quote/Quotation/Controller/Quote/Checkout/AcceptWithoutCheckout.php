<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Checkout;

/**
 * Class Accept
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
class AcceptWithoutCheckout extends DefaultCheckout
{
    /**
     * Redirect to customer checkout page if the quotation customer is the same customer as logged in
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->initQuote();

        if ($this->isAutoLogin() && $this->hasValidHash()) {
            $this->autoLogin();

            return $this->proceedToAcceptQuotation();
        }

        if ($this->isSameCustomer()) {
            return $this->proceedToAcceptQuotation();
        }

        return $this->defaultRedirect();
    }
}
