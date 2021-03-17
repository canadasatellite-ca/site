<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ServicesConnector\Model\EnvironmentFactory;

class ServicesConnectorConfiguration
{

    /**
     * Amazon Sales Channel module name
     */
    const MODULE_NAME = 'Magento_Amazon';

    /**
     *  Services Connector use sandbox config path
     */
    const XML_PATH_USE_SERVICES_CONNECTOR_SANDBOX_API_KEY = 'channel/amazon/endpoint/use_services_connector_sandbox_api_key';
    /**
     *  Use Services Connector config path
     */
    const XML_PATH_USE_SERVICES_CONNECTOR = 'channel/amazon/endpoint/use_services_connector';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EnvironmentFactory
     */
    private $environmentFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EnvironmentFactory $environmentFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EnvironmentFactory $environmentFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->environmentFactory = $environmentFactory;
    }

    /**
     * Returns Services Connector environment string.
     *
     * @return string
     */
    public function getServicesConnectorEnvironment(): string
    {
        $shouldUseServicesConnectorSandboxApiKey = $this->getUseServicesConnectorSandboxApiKeyConfig();

        return $shouldUseServicesConnectorSandboxApiKey ? 'sandbox' : 'production';
    }

    /**
     * Returns the gateway url
     * Note: This function can be deleted once ServicesConnector extension exposes the URL using an interface
     * @return string
     */
    public function getGatewayUrl(): string
    {
        $environment = $this->environmentFactory->create($this->getServicesConnectorEnvironment());

        return $environment->getGatewayUrl();
    }

    /**
     * Returns config for using Services Connector Sandbox API key over
     * Production API key.
     *
     * @return bool
     */
    private function getUseServicesConnectorSandboxApiKeyConfig(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_SERVICES_CONNECTOR_SANDBOX_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Returns config for using Services Connector
     * Production API key.
     *
     * @return bool
     */
    public function getUseServicesConnector(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_USE_SERVICES_CONNECTOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}
