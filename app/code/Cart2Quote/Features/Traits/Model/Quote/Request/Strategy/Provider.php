<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Request\Strategy;
use Cart2Quote\Quotation\Model\Strategy\StrategyInterface;
/**
 * Trait Provider
 *
 * @package Cart2Quote\Quotation\Model\Quote\Strategy
 */
trait Provider
{
    /**
     * Get quote request strategy
     *
     * @return StrategyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStrategy()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$type = $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_QUOTE_STRATEGY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!isset($this->strategies[$type])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Quote request strategy does not exist'));
        }
        return $this->strategies[$type];
		}
	}
}
