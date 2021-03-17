<?php

namespace BroSolutions\CheckoutReCapcha\Model;

class Config
{
    const XML_PATH_ENABLED_FRONTEND_CHECKOUT_PAYMENT = 'msp_securitysuite_recaptcha/frontend/enabled_checkout_payment';

    private $scopeConfig;

    private $reCaptchaConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \MSP\ReCaptcha\Model\Config $reCaptchaConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->reCaptchaConfig = $reCaptchaConfig;
    }

    public function isEnabledFrontendCheckoutPayment()
    {
        if (!$this->reCaptchaConfig->isEnabledFrontend()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(static::XML_PATH_ENABLED_FRONTEND_CHECKOUT_PAYMENT);
    }
}
