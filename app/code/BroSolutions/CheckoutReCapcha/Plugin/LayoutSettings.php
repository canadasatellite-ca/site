<?php

namespace BroSolutions\CheckoutReCapcha\Plugin;

class LayoutSettings
{
    protected $config;

    public function __construct(\BroSolutions\CheckoutReCapcha\Model\Config $config)
    {
        $this->config = $config;
    }

    public function afterGetCaptchaSettings(
        \MSP\ReCaptcha\Model\LayoutSettings $subject,
        array $result
    ) {
        $result['enabled']['checkout'] = $this->config->isEnabledFrontendCheckoutPayment();

        return $result;
    }
}
