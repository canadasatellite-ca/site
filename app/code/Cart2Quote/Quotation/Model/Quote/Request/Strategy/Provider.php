<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Request\Strategy;

use Cart2Quote\Quotation\Model\Strategy\StrategyInterface;

/**
 * Class Provider
 *
 * @package Cart2Quote\Quotation\Model\Quote\Strategy
 */
class Provider implements \Cart2Quote\Quotation\Model\Strategy\ProviderInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Request\Strategy\Provider {
        getStrategy as private traitGetStrategy;
    }

    /**
     * system.xml path to quote request strategy
     */
    const XML_CONFIG_PATH_QUOTE_STRATEGY = 'cart2quote_quotation/global/quote_request_strategy';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var array
     */
    private $strategies;

    /**
     * Provider constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $strategies
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $strategies = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->strategies = $strategies;
    }

    /**
     * Get quote request strategy
     *
     * @return StrategyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStrategy()
    {
        return $this->traitGetStrategy();
    }
}
