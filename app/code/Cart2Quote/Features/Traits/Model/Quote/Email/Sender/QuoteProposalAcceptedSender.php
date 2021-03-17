<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email\Sender;
/**
 * Trait QuoteProposalAcceptedSender
 */
trait QuoteProposalAcceptedSender
{
    /**
     * Send function overwrite
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param bool $forceSyncMode
     * @return bool
     */
    private function send(\Cart2Quote\Quotation\Model\Quote $quote, $forceSyncMode = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return parent::send($quote, $forceSyncMode);
		}
	}
}
