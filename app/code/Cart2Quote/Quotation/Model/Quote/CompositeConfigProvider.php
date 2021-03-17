<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class CompositeConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
class CompositeConfigProvider extends \Magento\Checkout\Model\CompositeConfigProvider
{
    use \Cart2Quote\Features\Traits\Model\Quote\CompositeConfigProvider {
        getAllowedConfigProviders as private traitGetAllowedConfigProviders;
    }

    /**
     * Replace the default config provider to get from the quote session instead of the checkout session
     *
     * @param \Cart2Quote\Quotation\Model\Quote\ConfigProvider $quotationSessionConfigProvider
     * @param \Cart2Quote\Quotation\Model\Quote\QuotationConfigProvider $quotationConfigProvider
     * @param array $configProviders
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\ConfigProvider $quotationSessionConfigProvider,
        \Cart2Quote\Quotation\Model\Quote\QuotationConfigProvider $quotationConfigProvider,
        array $configProviders
    ) {
        $configProviders['checkout_default_config_provider'] = $quotationSessionConfigProvider;
        $configProviders['quotation_config_provider'] = $quotationConfigProvider;
        $configProviders = array_intersect_key($configProviders, $this->getAllowedConfigProviders());

        parent::__construct($configProviders);
    }

    /**
     * Get the allowed config providers
     * - Other config providers are ignored.
     *
     * @return array
     */
    protected function getAllowedConfigProviders()
    {
        return $this->traitGetAllowedConfigProviders();
    }
}
