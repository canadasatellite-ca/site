<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service;

use Magento\Amazon\Model\ServicesConnectorConfiguration;
use Magento\ServicesConnector\Api\ConfigInterface;
use Magento\ServicesConnector\Api\KeyNotFoundException;
use Magento\ServicesConnector\Api\KeyValidationInterface;

class ServicesConnector
{
    /**
     * @var KeyValidationInterface
     */
    private $keyValidation;
    /**
     * @var ServicesConnectorConfiguration
     */
    private $servicesConnectorConfiguration;
    /**
     * @var ConfigInterface
     */
    private $servicesConnectorConfig;

    /**
     * @param KeyValidationInterface $keyValidation
     * @param ServicesConnectorConfiguration $servicesConnectorConfiguration
     * @param ConfigInterface $servicesConnectorConfig
     */
    public function __construct(
        KeyValidationInterface $keyValidation,
        ServicesConnectorConfiguration $servicesConnectorConfiguration,
        ConfigInterface $servicesConnectorConfig
    ) {
        $this->keyValidation = $keyValidation;
        $this->servicesConnectorConfiguration = $servicesConnectorConfiguration;
        $this->servicesConnectorConfig = $servicesConnectorConfig;
    }

    public function isApiKeyValid(): bool
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

    public function getApiPortalUrl(): string
    {
        return  $this->servicesConnectorConfig->getApiPortalUrl();
    }

    public function getKeyConfigUrl(): string
    {
        return  $this->servicesConnectorConfig->getKeyConfigPage(
            $this->servicesConnectorConfiguration::MODULE_NAME,
            $this->servicesConnectorConfiguration->getServicesConnectorEnvironment()
        );
    }
}
