<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Captcha\Magento;

use Aheadworks\AdvancedReviews\Model\Captcha\CaptchaAdapterInterface;
use Magento\Captcha\Helper\Data as CaptchaData;
use Magento\Framework\App\RequestInterface;

/**
 * Class Captcha
 * @package Aheadworks\AdvancedReviews\Model\Captcha\Magento
 */
class Captcha implements CaptchaAdapterInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CaptchaData
     */
    private $captchaData;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $formId;

    /**
     * @param ConfigProvider $configProvider
     * @param CaptchaData $captchaData
     * @param RequestInterface $request
     * @param string $formId
     */
    public function __construct(
        ConfigProvider $configProvider,
        CaptchaData $captchaData,
        RequestInterface $request,
        string $formId = CaptchaAdapterInterface::DEFAULT_FORM_ID
    ) {
        $this->configProvider = $configProvider;
        $this->captchaData = $captchaData;
        $this->request = $request;
        $this->formId = $formId;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->configProvider->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function getLayoutConfig()
    {
        return [
            'component' => 'Aheadworks_AdvancedReviews/js/captcha/magento/captcha',
            'formId' => $this->formId,
            'configSource' => 'awAr'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        return $this->configProvider->getConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $captcha = $this->captchaData->getCaptcha($this->formId);
        $captchaString = $this->request->getParam('captcha_string');
        return $captcha->isCorrect($captchaString);
    }
}
