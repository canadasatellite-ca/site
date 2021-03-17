<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;

class Attributes implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Attributes
     */
    private $attributesDataProvider;

    /**
     * Attributes constructor.
     * @param \Magento\Amazon\GraphQl\DataProvider\Attributes $attributesDataProvider
     */
    public function __construct(\Magento\Amazon\GraphQl\DataProvider\Attributes $attributesDataProvider)
    {
        $this->attributesDataProvider = $attributesDataProvider;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        if ($parent) {
            return $parent;
        }
        $fields = $info->getFieldSelection(4);
        $attributesRequest = new \Magento\Amazon\GraphQl\DataProvider\AttributesRequest($fields);
        $limit = isset($args['first']) ? (int)$args['first'] : null;
        $attributesRequest->setLimit($limit);
        $attributesRequest->setCursor($args['after'] ?? null);
        $attributesRequest->setFilters($args['filters'] ?? []);
        return new \GraphQL\Deferred(function () use ($attributesRequest) {
            $attributesResult = $this->attributesDataProvider->resolveAttributes($attributesRequest);
            $response = [];
            if ($attributesRequest->isCalculateTotalCount()) {
                $response['totalCount'] = $attributesResult->getTotalCount();
            }
            $response['edges'] = $attributesResult->getAttributesData();
            $response['pageInfo'] = [
                'endCursor' => $attributesResult->getEndCursor(),
                'hasNextPage' => $attributesResult->isHasNextPage(),
            ];
            return $response;
        });
    }
}
