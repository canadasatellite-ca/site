<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class MagentoUrl implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var \Magento\Amazon\Service\BuildBackendUrl
     */
    private $buildBackendUrl;

    /**
     * MagentoUrl constructor.
     * @param JsonSerializer $jsonSerializer
     * @param \Magento\Amazon\Service\BuildBackendUrl $buildBackendUrl
     */
    public function __construct(JsonSerializer $jsonSerializer, \Magento\Amazon\Service\BuildBackendUrl $buildBackendUrl)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->buildBackendUrl = $buildBackendUrl;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $route = $args['route'];
        $urlParameters = isset($args['params']) ? $this->jsonSerializer->unserialize($args['params']) : [];
        $query = isset($args['query']) ? $this->jsonSerializer->unserialize($args['query']) : [];
        $fragment = $args['urlFragment'] ?? null;
        return $this->buildBackendUrl->getUrl($route, $urlParameters, $query, $fragment);
    }
}
