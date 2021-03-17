<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
/**
 * Trait ConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait ConfigProvider
{
    /**
     * Retrieve checkout URL
     *
     * @return string
     */
    private function getCheckoutUrl()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->urlBuilder->getUrl('quotation/quote/index');
		}
	}
    /**
     * Retrieve checkout URL
     *
     * @return string
     */
    private function pageNotFoundUrl()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->urlBuilder->getUrl('quotation/quote/index');
		}
	}
    /**
     * Retrieve default success page URL
     *
     * @return string
     */
    private function getDefaultSuccessPageUrl()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->urlBuilder->getUrl('quotation/quote/success/');
		}
	}
}
