<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Captcha\Google;

use Aheadworks\AdvancedReviews\Model\Captcha\CaptchaAdapterInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Captcha
 * @package Aheadworks\AdvancedReviews\Model\Captcha\Google
 */
class Captcha implements CaptchaAdapterInterface
{
    /**
     * @var \MSP\ReCaptcha\Model\LayoutSettings
     */
    private $layoutSettings;

    /**
     * @var \MSP\ReCaptcha\Model\Config
     */
    private $config;

    /**
     * @var \MSP\ReCaptcha\Api\ValidateInterface
     */
    private $validate;

    /**
     * @var \MSP\ReCaptcha\Model\Provider\ResponseProviderInterface
     */
    private $responseProvider;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $formId;

    /**
     * @param RemoteAddress $remoteAddress
     * @param ObjectManagerInterface $objectManager
     * @param string $formId
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        ObjectManagerInterface $objectManager,
        string $formId = CaptchaAdapterInterface::DEFAULT_FORM_ID
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->objectManager = $objectManager;
        $this->formId = $formId;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->getConfig()->isEnabledFrontend();
    }

    /**
     * {@inheritdoc}
     */
    public function getLayoutConfig()
    {
        return [
            'component' => 'Aheadworks_AdvancedReviews/js/captcha/google/captcha',
            'reCaptchaId' => $this->formId,
            'zone' => $this->formId,
            'settings' => array_merge(
                $this->getLayoutSettings()->getCaptchaSettings(),
                ['enabled' => [$this->formId => true]]
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $reCaptchaResponse = $this->getResponseProvider()->execute();
        $remoteIp = $this->remoteAddress->getRemoteAddress();
        return $this->getValidate()->validate($reCaptchaResponse, $remoteIp);
    }

    /**
     * Retrieve config instance
     *
     * @return \MSP\ReCaptcha\Model\Config
     */
    private function getConfig()
    {
        if (null === $this->config) {
            $this->config = $this->objectManager->create(\MSP\ReCaptcha\Model\Config::class);
        }
        return $this->config;
    }

    /**
     * Retrieve config instance
     *
     * @return \MSP\ReCaptcha\Model\LayoutSettings
     */
    private function getLayoutSettings()
    {
        if (null === $this->layoutSettings) {
            $this->layoutSettings = $this->objectManager->create(\MSP\ReCaptcha\Model\LayoutSettings::class);
        }
        return $this->layoutSettings;
    }

    /**
     * Retrieve config instance
     *
     * @return \MSP\ReCaptcha\Model\Provider\ResponseProviderInterface
     */
    private function getResponseProvider()
    {
        if (null === $this->responseProvider) {
            $this->responseProvider = $this->objectManager->create(
                \MSP\ReCaptcha\Model\Provider\ResponseProviderInterface::class
            );
        }
        return $this->responseProvider;
    }

    /**
     * Retrieve config instance
     *
     * @return \MSP\ReCaptcha\Api\ValidateInterface
     */
    private function getValidate()
    {
        if (null === $this->validate) {
            $this->validate = $this->objectManager->create(\MSP\ReCaptcha\Api\ValidateInterface::class);
        }
        return $this->validate;
    }
}
