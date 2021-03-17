<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin;

use Magento\Checkout\Model\Session;

/**
 * Class CheckoutProcessor
 * @package Cart2Quote\Quotation\Plugin
 */
class CheckoutProcessor
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * CheckoutProcessor constructor
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Checkout LayoutProcessor after process plugin
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $processor
     * @param $jsLayout
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $processor, $jsLayout)
    {
        $quote = $this->checkoutSession->getQuote();

        $shippingConfig = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress'];
        $paymentConfig = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment'];

        if (!$quote->isVirtual()
            && isset($shippingConfig['component'])
            && (strpos($shippingConfig['component'], 'Amazon_Payment') !== false)
        ) {
            if (isset($shippingConfig['template']) && ($shippingConfig['template'] == 'Cart2Quote_Quotation/quote-checkout/shipping')) {
                $shippingConfig['component'] = 'Cart2Quote_Quotation/js/quote-checkout/view/shipping';
                $shippingConfig['children']['customer-email']['component'] = 'Cart2Quote_Quotation/js/quote-checkout/view/form/email';
                $shippingConfig['children']['address-list']['component'] = 'Magento_Checkout/js/view/shipping-address/list';
                if (isset($shippingConfig['children']['shipping-address-fieldset']['children']['inline-form-manipulator'])) {
                    unset($shippingConfig['children']['shipping-address-fieldset']['children']['inline-form-manipulator']);
                }

                if (isset($paymentConfig['children']['payments-list']['component'])) {
                    $paymentConfig['children']['payments-list']['component'];
                }
            }
        }

        return $jsLayout;
    }
}
