<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Admin\Quote;
/**
 * Trait EmailSender
 */
trait EmailSender
{
    /**
     * Send email about new quote.
     * - Process mail exception
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    private function send(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			try {
            $this->quoteRequestSender->send($quote);
        } catch (\Magento\Framework\Exception\MailException $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addWarning(
                __('You did not email your customer. Please check your email settings.')
            );
            return false;
        }
        return true;
		}
	}
}
