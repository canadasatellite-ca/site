<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\QuickQuote;
/**
 * Trait ConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\QuickQuote
 */
trait ConfigProvider
{
    /**
     * Get config
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getConfig()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return [
            'formKey' => $this->formKey->getFormKey(),
            'storeCode' => $this->storeManager->getStore()->getCode(),
            'isCustomerLoggedIn' => $this->isCustomerLoggedIn(),
            'registeredQuoteCheckout' => $this->addressHelper->getRegisteredQuoteCheckoutConfig(),
            'showRemark' => true,
            /**
             * customerData array is needed in check-email-availability js component but the data is depersonalized
             * @see \Magento\Customer\Model\Layout\DepersonalizePlugin
             */
            'customerData' => []
        ];
		}
	}
    /**
     * Check if customer is logged in
     *
     * @return bool
     * @codeCoverageIgnore
     */
    private function isCustomerLoggedIn()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
		}
	}
}
