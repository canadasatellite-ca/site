<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Source\Quote\Request\Strategy\Option;
/**
 * Trait QuickQuote
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Quote\Request\Strategy\Option
 */
trait QuickQuote
{
    /**
     * Get Label
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getLabel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return __('Quick Quote');
		}
	}
    /**
     * Get Comment
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getComment()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return sprintf(
            '<b>%s:</b> %s',
            $this->getLabel(),
            __('Request a quote directly from a product page via a popup')
        );
		}
	}
}
