<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
use Magento\Checkout\Model\Session as CheckoutSession;
/**
 * Trait GoToCheckoutConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait GoToCheckoutConfigProvider
{
    /**
     * Get the config for the checkout provider
     * - The below function adds the following to the config provider:
     * - quotationCustomerData - this is for the guest checkout: the first name, last name and email
     * - quotationGuestCheckout - flag for guest
     * - isGuestCheckoutAllowed - Magento flag for guest
     *
     * @return array
     */
    private function getConfig()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$output = [];
        $quotationQuoteId = $this->checkoutSession->getQuotationQuoteId();
        $checkoutQuoteId = $this->checkoutSession->getQuote()->getId();
        if (($quotationQuoteId == $checkoutQuoteId) && $this->checkoutSession->getQuote()->getCustomerIsGuest()) {
            $output['quotationCustomerData'] = $this->getCustomerData();
            $output['quotationGuestCheckout'] = true;
            $output['isGuestCheckoutAllowed'] = true;
        } else {
            $output['quotationCustomerData'] = [];
            $output['quotationGuestCheckout'] = false;
        }
        if ($this->quotationHelper->isQuotationCouponDisabled()){
            $output['quotationQuoteCouponDisabled'] = true;
        }
        return $output;
		}
	}
    /**
     * Retrieve customer data
     *
     * @return array
     */
    private function getCustomerData()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$customerData = [];
        if ($this->checkoutSession->getQuote()->getData()) {
            foreach ($this->checkoutSession->getQuote()->getData() as $key => $value) {
                $keyExploded = explode('_', $key);
                if ($keyExploded[0] == 'customer') {
                    unset($keyExploded[0]);
                    $newKey = implode('_', $keyExploded);
                    $customerData[$newKey] = $value;
                }
            }
        }
        return $customerData;
		}
	}
}
