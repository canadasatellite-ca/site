<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
/**
 * Trait CompositeConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait CompositeConfigProvider
{
    /**
     * Get the allowed config providers
     * - Other config providers are ignored.
     *
     * @return array
     */
    private function getAllowedConfigProviders()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return array_fill_keys([
            'checkout_default_config_provider',
            'quotation_config_provider',
            'checkout_default_config_provider',
            'persistent_checkout_config_provider',
            'checkout_captcha_config_provider',
            'tax_config_provider',
            'weee_config_provider',
            'checkout_captcha_config_provider',
            'checkout_klarna_kp_config',
            'amasty_order_attribute_config_provider'
        ], "");
		}
	}
}
