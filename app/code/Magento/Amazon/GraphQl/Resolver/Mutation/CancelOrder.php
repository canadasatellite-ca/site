<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Service\Order\CancelOrder as CancelOrderService;

class CancelOrder implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var CancelOrderService
     */
    private $cancelOrderService;
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Stores
     */
    private $stores;

    public function __construct(
        CancelOrderService $cancelOrder,
        \Magento\Amazon\GraphQl\DataProvider\Stores $stores
    ) {
        $this->cancelOrderService = $cancelOrder;
        $this->stores = $stores;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $uuid = $args['uuid'];
        $orderId = $args['orderId'];
        $reason = $args['reason'];

        $this->cancelOrderService->cancelOrder($orderId, $reason);
        $context->stores()->addSingle($uuid, $info);
        return $this->stores->getSingleStore($uuid, $context);
    }
}
