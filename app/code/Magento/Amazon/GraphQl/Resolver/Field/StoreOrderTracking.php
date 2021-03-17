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
use Magento\Amazon\GraphQl\DataProvider\StoreOrderTracking as StoreOrderTrackingProvider;

class StoreOrderTracking implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var StoreOrderTrackingProvider
     */
    private $dataProvider;

    public function __construct(StoreOrderTrackingProvider $dataProvider)
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
        $orderId = $parent['orderId'];
        $context->storeOrderTracking()->addSingle($orderId, $info);
        return new Deferred(function () use ($context, $orderId) {
            return $this->dataProvider->getOrderTrackingByOrderId($context, $orderId);
        });
    }
}
