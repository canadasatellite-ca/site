<?php

namespace BroSolutions\CheckoutReCapcha\Block\LayoutProcessor\Checkout;

class Onepage implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    private $layoutSettings;

    public function __construct(
        \MSP\ReCaptcha\Model\LayoutSettings $layoutSettings
    ) {
        $this->layoutSettings = $layoutSettings;
    }

    public function process($jsLayout)
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['afterMethods']['children']['msp_recaptcha_checkout_payment']['settings'] = $this->layoutSettings->getCaptchaSettings();

        return $jsLayout;
    }
}
