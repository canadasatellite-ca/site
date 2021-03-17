<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;

use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class GoToCheckoutConfigProvider
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
class GoToCheckoutConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\GoToCheckoutConfigProvider {
        getConfig as private traitGetConfig;
        getCustomerData as private traitGetCustomerData;
    }

    /**
     * Checkout session
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Cart2Quote\Quotation\Helper\Data
     */
    private $quotationHelper;

    /**
     * GoToCheckoutConfigProvider constructor
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quotationHelper = $quotationHelper;
    }

    /**
     * Get the config for the checkout provider
     * - The below function adds the following to the config provider:
     * - quotationCustomerData - this is for the guest checkout: the first name, last name and email
     * - quotationGuestCheckout - flag for guest
     * - isGuestCheckoutAllowed - Magento flag for guest
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->traitGetConfig();
    }

    /**
     * Retrieve customer data
     *
     * @return array
     */
    private function getCustomerData()
    {
        return $this->traitGetCustomerData();
    }
}
