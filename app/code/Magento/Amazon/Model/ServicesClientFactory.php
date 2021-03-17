<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model;

use Magento\ServicesConnector\Api\ClientResolverInterface;

/**
 * Class ServicesClientFactory
 */
class ServicesClientFactory
{
    /**
     * @var ClientResolverInterface
     */
    private $clientResolver;

    /**
     * @var ServicesConnectorConfiguration
     */
    private $servicesConnectorConfiguration;

    /**
     * ServicesClient constructor.
     * @param ClientResolverInterface $clientResolver
     * @param ServicesConnectorConfiguration $servicesConnectorConfiguration
     */
    public function __construct(
        ClientResolverInterface $clientResolver,
        ServicesConnectorConfiguration $servicesConnectorConfiguration
    ) {
        $this->clientResolver = $clientResolver;
        $this->servicesConnectorConfiguration = $servicesConnectorConfiguration;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function create(): \GuzzleHttp\Client
    {
        $environment = $this->servicesConnectorConfiguration->getServicesConnectorEnvironment();
        $moduleName = ServicesConnectorConfiguration::MODULE_NAME;
        return $this->clientResolver->createHttpClient($moduleName, $environment);
    }
}
