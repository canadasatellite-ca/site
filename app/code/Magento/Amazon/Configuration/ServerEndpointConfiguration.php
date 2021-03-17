<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Configuration;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ServicesConnectorConfiguration;
use Magento\Framework\App\Config\ScopeConfigInterface as Config;

/**
 * Class to get configuration of server endpoints
 */
class ServerEndpointConfiguration
{
    /**
     * API base URL config path
     */
    const XML_PATH_HOST_BASE_DIRECTORY = 'channel/amazon/endpoint/base_directory';

    /**
     * API Endpoints
     */
    const API_ENDPOINT_MERCHANT = '/merchant/:uuid';
    const API_ENDPOINT_MERCHANT_CREATE = '/merchant';
    const API_ENDPOINT_MERCHANT_IRP_URL = '/merchant/:uuid/irp-url';
    const API_ENDPOINT_MERCHANT_SELLER_ID = '/merchant/:uuid/seller-id';
    const API_ENDPOINT_MERCHANT_AUTHENTICATION_STATUS = '/merchant/:uuid/authentication-status';
    const API_ENDPOINT_LOGS = '/merchant/:uuid/log';
    const API_ENDPOINT_LOGS_BULK_DELETE = '/merchant/:uuid/log/bulk-delete';
    const API_ENDPOINT_COMMANDS = '/merchant/:uuid/command';
    const API_VERSION = 'v2';

    /**
     * @var Config
     */
    private $scopeConfig;

    /**
     * @var ServicesConnectorConfiguration
     */
    private $servicesConnectorConfiguration;

    /**
     * ServerEndpointConfiguration constructor.
     * @param Config $scopeConfig
     * @param ServicesConnectorConfiguration $servicesConnectorConfiguration
     */
    public function __construct(
        Config $scopeConfig,
        ServicesConnectorConfiguration $servicesConnectorConfiguration
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->servicesConnectorConfiguration = $servicesConnectorConfiguration;
    }

    /**
     * @return string
     */
    private function getBaseUrl(): string
    {
        return $this->servicesConnectorConfiguration->getGatewayUrl();
    }

    /**
     * @return string
     */
    private function getBaseDirectory(): string
    {
        $baseDir = (string)$this->scopeConfig->getValue(
            self::XML_PATH_HOST_BASE_DIRECTORY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        return $baseDir . '/';
    }

    /**
     * @param string $countryCode
     * @param string $uuid
     * @param string $endpoint
     * @param string $apiVersion
     * @return string
     */
    private function getUrlForEndpoint(
        string $countryCode,
        string $uuid,
        string $endpoint,
        string $apiVersion = self::API_VERSION
    ): string {
        $endpoint = str_replace(':uuid', $uuid, $endpoint);
        $nodes = [
            $this->getBaseUrl(),
            $this->getBaseDirectory(),
            strtolower(Definitions::getRegionName($countryCode)),
            $apiVersion,
            $endpoint,
        ];
        $nodes = array_map(static function ($urlPart) {
            return trim($urlPart, '/');
        }, $nodes);
        return implode('/', array_filter($nodes));
    }

    /**
     * Returns merchant endpoint.
     *
     * @param string $countryCode
     * @param string $uuid
     * @return string
     */
    public function merchant(string $countryCode, string $uuid): string
    {
        return $this->getUrlForEndpoint($countryCode, $uuid, self::API_ENDPOINT_MERCHANT);
    }

    /**
     * Returns create merchant endpoint.
     *
     * @param string $countryCode
     * @return string
     */
    public function merchantCreate(string $countryCode): string
    {
        return $this->getUrlForEndpoint($countryCode, '', self::API_ENDPOINT_MERCHANT_CREATE);
    }

    /**
     * Returns merchant endpoint which will redirect to amazon irp.
     *
     * @param string $countryCode
     * @param string $uuid
     * @return string
     */
    public function merchantIrpUrl(string $countryCode, string $uuid): string
    {
        return $this->getUrlForEndpoint($countryCode, $uuid, self::API_ENDPOINT_MERCHANT_IRP_URL);
    }

    /**
     * Returns merchant seller id endpoint.
     *
     * @param string $countryCode
     * @param string $uuid
     * @return string
     */
    public function merchantSellerId(string $countryCode, string $uuid): string
    {
        return $this->getUrlForEndpoint($countryCode, $uuid, self::API_ENDPOINT_MERCHANT_SELLER_ID);
    }

    /**
     * Returns merchant authentication status endpoint.
     *
     * @param string $countryCode
     * @param string $uuid
     * @return string
     */
    public function merchantAuthenticationStatus(string $countryCode, string $uuid): string
    {
        return $this->getUrlForEndpoint($countryCode, $uuid, self::API_ENDPOINT_MERCHANT_AUTHENTICATION_STATUS);
    }

    /**
     * Returns commands endpoint.
     *
     * @param string $countryCode
     * @param string $uuid
     * @return string
     */
    public function commands(string $countryCode, string $uuid): string
    {
        return $this->getUrlForEndpoint($countryCode, $uuid, self::API_ENDPOINT_COMMANDS);
    }

    /**
     * Returns fetch endpoint.
     *
     * @param string $countryCode
     * @param string $uuid
     * @return string
     */
    public function logs(string $countryCode, string $uuid): string
    {
        return $this->getUrlForEndpoint($countryCode, $uuid, self::API_ENDPOINT_LOGS);
    }

    /**
     * Returns fetch endpoint.
     *
     * @param string $countryCode
     * @param string $uuid
     * @return string
     */
    public function logsBulkDelete(string $countryCode, string $uuid): string
    {
        return $this->getUrlForEndpoint($countryCode, $uuid, self::API_ENDPOINT_LOGS_BULK_DELETE);
    }
}
