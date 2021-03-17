<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Captcha\Magento;

use Magento\Captcha\Model\Checkout\ConfigProvider as CaptchaConfigProvider;

/**
 * Class ConfigProvider
 * @package Aheadworks\AdvancedReviews\Model\Captcha\Magento
 */
class ConfigProvider extends CaptchaConfigProvider
{
    /**
     * Check if is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->captchaData->getConfig('enable');
    }

    /**
     * {@inheritdoc}
     */
    protected function isRequired($formId)
    {
        return $this->isEnabled();
    }
}
