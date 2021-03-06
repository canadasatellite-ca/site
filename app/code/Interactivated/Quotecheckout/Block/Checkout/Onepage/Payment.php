<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Onepage;

class Payment extends \Interactivated\Quotecheckout\Block\Checkout\Onepage\AbstractOnepage
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData(
            'payment',
            ['label' => __('Payment Information'), 'is_show' => $this->isShow()]
        );
        parent::_construct();
    }

    /**
     * Getter
     *
     * @return float
     */
    function getQuoteBaseGrandTotal()
    {
        return (double)$this->getQuote()->getBaseGrandTotal();
    }

    /**
     * Get options
     *
     * @return array
     */
    function getOptions()
    {
        $registerParam = $this->getRequest()->getParam('register');
        return [
            'quoteBaseGrandTotal' => $this->getQuoteBaseGrandTotal(),
            'progressUrl' => $this->getUrl('checkout/onepage/progress'),
            'reviewUrl' => $this->getUrl('checkout/onepage/review'),
            'failureUrl' => $this->getUrl('checkout/cart'),
            'getAddressUrl' => $this->getUrl('checkout/onepage/getAddress') . 'address/',
            'checkout' => [
                'suggestRegistration' => $registerParam || $registerParam === '',
                'saveUrl' => $this->getUrl('checkout/onepage/saveMethod'),
            ],
            'billing' => ['saveUrl' => $this->getUrl('checkout/onepage/saveBilling')],
            'shipping' => ['saveUrl' => $this->getUrl('checkout/onepage/saveShipping')],
            'shippingMethod' => ['saveUrl' => $this->getUrl('checkout/onepage/saveShippingMethod')],
            'payment' => [
                'defaultPaymentMethod' => $this->getChildBlock('methods')->getSelectedMethodCode(),
                'saveUrl' => $this->getUrl('checkout/onepage/savePayment'),
            ],
            'review' => [
                'saveUrl' => $this->getUrl('checkout/onepage/saveOrder'),
                'successUrl' => $this->getUrl('checkout/onepage/success'),
            ]
        ];
    }
}
