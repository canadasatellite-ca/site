<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Captcha;

use Aheadworks\AdvancedReviews\Model\Config;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReviews\Model\Captcha\Google\Captcha as GoogleCaptcha;
use Aheadworks\AdvancedReviews\Model\Captcha\Magento\Captcha as MagentoCaptcha;

/**
 * Class Factory
 * @package Aheadworks\AdvancedReviews\Model\Captcha
 */
class Factory
{
    /**
     * Google Re Captcha module Name
     */
    const GOOGLE_RE_CAPTCHA_MODULE_NAME = 'MSP_ReCaptcha';

    /**
     * Magento Captcha module Name
     */
    const MAGENTO_CAPTCHA_MODULE_NAME = 'Magento_Captcha';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Config $config
     * @param ModuleListInterface $moduleList
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Config $config,
        ModuleListInterface $moduleList,
        ObjectManagerInterface $objectManager
    ) {
        $this->config = $config;
        $this->moduleList = $moduleList;
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance
     *
     * @param string $formId
     * @return bool|CaptchaAdapterInterface
     */
    public function create($formId)
    {
        $instance = false;
        if ($this->config->isEnableCaptcha()) {
            $args = ['formId' => $formId];
            if ($this->moduleList->has(self::GOOGLE_RE_CAPTCHA_MODULE_NAME)) {
                $instance = $this->objectManager->create(
                    GoogleCaptcha::class,
                    $args
                );
            } elseif ($this->moduleList->has(self::MAGENTO_CAPTCHA_MODULE_NAME)) {
                $instance = $this->objectManager->create(
                    MagentoCaptcha::class,
                    $args
                );
            }
        }
        return $instance;
    }
}
