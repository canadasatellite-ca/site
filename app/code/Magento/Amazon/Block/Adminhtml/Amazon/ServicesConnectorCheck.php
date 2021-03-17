<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Block\Adminhtml\Amazon;

use Magento\Amazon\Model\ServicesConnectorConfiguration;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\ServicesConnector\Api\ConfigInterface;
use Magento\ServicesConnector\Api\KeyNotFoundException;
use Magento\ServicesConnector\Api\KeyValidationInterface;

/**
 * Class ServicesConnectorCheck
 */
class ServicesConnectorCheck extends Template
{
    /**
     * API key FAQ URL
     */
    const URL_MAGENTO_API_KEY_FAQ = "https://docs.magento.com/m2/ee/user_guide/sales-channels/amazon/amazon-verify-api-key.html";

    /**
     * @var ConfigInterface $configInterface
     */
    private $configInterface;
    /**
     * @var KeyValidationInterface $keyValidation
     */
    private $keyValidation;
    /**
     * @var ServicesConnectorConfiguration $servicesConnectorConfiguration
     */
    private $servicesConnectorConfiguration;

    /**
     * @param Context $context
     * @param ConfigInterface $configInterface
     * @param KeyValidationInterface $keyValidation
     * @param ServicesConnectorConfiguration $servicesConnectorConfiguration
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $configInterface,
        KeyValidationInterface $keyValidation,
        ServicesConnectorConfiguration $servicesConnectorConfiguration,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configInterface = $configInterface;
        $this->keyValidation = $keyValidation;
        $this->servicesConnectorConfiguration = $servicesConnectorConfiguration;
    }

    /**
     * Returns the API key FAQ URL
     *
     * @return string
     */
    public function getApiKeyFaqUrl(): string
    {
        return self::URL_MAGENTO_API_KEY_FAQ;
    }

    /**
     * Returns the API portal URL
     *
     * @return string
     */
    public function getApiPortalUrl(): string
    {
        return $this->configInterface->getApiPortalUrl();
    }

    /**
     * Returns the API key config page URL
     *
     * @return string
     */
    public function getApiKeyConfigPageUrl(): string
    {
        return $this->configInterface->getKeyConfigPage(
            $this->servicesConnectorConfiguration::MODULE_NAME,
            $this->servicesConnectorConfiguration->getServicesConnectorEnvironment()
        );
    }

    /**
     * Returns true if current environment API key is valid, false otherwise.
     *
     * @return bool
     */
    public function isValidApiGatewayKey(): bool
    {
        try {
            if (!$this->servicesConnectorConfiguration->getUseServicesConnector()) {
                return true;
            }

            return $this->keyValidation->execute(
                $this->servicesConnectorConfiguration::MODULE_NAME,
                $this->servicesConnectorConfiguration->getServicesConnectorEnvironment()
            );
        } catch (KeyNotFoundException $e) {
            return false;
        }
    }
}
