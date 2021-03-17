<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use Magento\Amazon\Service\ServicesConnector as ServicesConnectorService;

class ServicesConnector implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var ServicesConnectorService
     */
    private $servicesConnector;
    /**
     * @var bool
     */
    private $isApiKeyValid;
    /**
     * @var string
     */
    private $apiPortalUrl;
    /**
     * @var string
     */
    private $keyConfigUrl;

    public function __construct(ServicesConnectorService $servicesConnector)
    {
        $this->servicesConnector = $servicesConnector;
    }

    public function resolve(
        $parent,
        array $args,
        \Magento\Amazon\GraphQl\Context $context,
        \GraphQL\Type\Definition\ResolveInfo $info
    ) {
        $response = [];
        $fields = $info->getFieldSelection();
        if (!empty($fields['isApiKeyValid'])) {
            $response['isApiKeyValid'] = $this->getKeyValidationResult();
        }
        if (!empty($fields['keyConfigUrl'])) {
            $response['keyConfigUrl'] = $this->getKeyConfigUrl();
        }
        if (!empty($fields['apiPortalUrl'])) {
            $response['apiPortalUrl'] = $this->getApiPortalUrl();
        }
        return $response;
    }

    private function getKeyValidationResult(): bool
    {
        if (null === $this->isApiKeyValid) {
            $this->isApiKeyValid = $this->servicesConnector->isApiKeyValid();
        }
        return $this->isApiKeyValid;
    }

    private function getApiPortalUrl(): string
    {
        if (null === $this->apiPortalUrl) {
            $this->apiPortalUrl = $this->servicesConnector->getApiPortalUrl();
        }
        return $this->apiPortalUrl;
    }

    private function getKeyConfigUrl(): string
    {
        if (null === $this->keyConfigUrl) {
            $this->keyConfigUrl = $this->servicesConnector->getKeyConfigUrl();
        }
        return $this->keyConfigUrl;
    }
}
