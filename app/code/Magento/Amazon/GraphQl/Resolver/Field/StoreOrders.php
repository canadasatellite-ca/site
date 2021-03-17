<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Field;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\DataProvider\StoreOrders as StoreOrdersDataProvider;

class StoreOrders implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var StoreOrdersDataProvider
     */
    private $dataProvider;

    public function __construct(StoreOrdersDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param $parent
     * @param array $args
     * @param Context $context
     * @param ResolveInfo $info
     * @return Deferred
     */
    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $id = (int)$parent['id'];
        $limit = (int) $args['first'];
        $filters = $args['filters'] ?? [];
        $cursor = $args['after'] ?? "";
        $sort = $args['sortBy'] ?? ['field' => 'purchaseDate', 'order' => 'DESC'];
        $arr = array_merge($filters, ['storeId' => $id, 'limit' => $limit, 'offset' => $cursor, 'sort' => $sort]);
        ksort($arr);
        $hashKey = hash('sha256', json_encode($arr));
        $context->storeOrders()->add($id, $limit, $cursor, $sort, $hashKey, $info);
        return new Deferred(function () use ($filters, $hashKey, $context) {
            $ordersResult = $this->dataProvider->getOrdersForStore($filters, $hashKey, $context);
            $response = [];
            if ($context->storeOrders()->isCalculateTotalCounts()->get($hashKey)) {
                $response['totalCount'] = $ordersResult->getTotalCount();
            }
            $response['edges'] = $ordersResult->getOrdersData();
            $response['pageInfo'] = [
                'endCursor' => $ordersResult->getEndCursor(),
                'hasNextPage' => $ordersResult->hasNextPage()
            ];
            return $response;
        });
    }
}
