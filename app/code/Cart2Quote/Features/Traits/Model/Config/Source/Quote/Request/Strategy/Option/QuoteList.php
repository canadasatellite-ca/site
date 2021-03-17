<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Source\Quote\Request\Strategy\Option;
/**
 * Trait QuoteList
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Quote\Request\Strategy\Option
 */
trait QuoteList
{
    /**
     * Get label
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getLabel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return __('Quote List');
		}
	}
    /**
     * Get comment
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getComment()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return sprintf(
            '<b>%s:</b> %s',
            $this->getLabel(),
            __('Add products to a quote list')
        );
		}
	}
}
