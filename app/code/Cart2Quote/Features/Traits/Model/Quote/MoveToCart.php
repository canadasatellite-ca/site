<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
/**
 * Trait MoveToCart
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait MoveToCart
{
    /**
     * Get url
     *
     * @param string $data
     * @return string
     */
    private function getUrl($data = '')
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->url->getUrl($data);
		}
	}
    /**
     * Clone quote model
     *
     * @return bool|\Magento\Quote\Model\Quote
     * @throws \Exception
     */
    private function cloneQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->checkoutSession->getQuote();
        $quotationQuote = $this->quotationSession->getQuote();
        if ($quotationQuote->getId()) {
            $this->stockCheckHelper->isMoveToCartAllowed($quote);
            $quote->merge($quotationQuote);
            $quote->collectTotals();
            $quote->save();
            $this->checkoutSession->setQuoteId($quote->getId());
            $quotationQuote->removeAllItems();
            $this->quoteRepository->save($quotationQuote);
        } else {
            throw new \Exception('This quote no longer exists.');
        }
        return $quote;
		}
	}
}
