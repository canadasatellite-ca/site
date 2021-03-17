<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

use Assert\AssertionFailedException;
use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\ValidationException;
use Magento\Amazon\Service\Order\UpdateOrder as UpdateOrderService;
use Magento\Amazon\Service\Order\UpdateOrderData;

class UpdateOrder implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var UpdateOrderService
     */
    private $updateOrderService;
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Stores
     */
    private $stores;

    public function __construct(
        UpdateOrderService $updateOrder,
        \Magento\Amazon\GraphQl\DataProvider\Stores $stores
    ) {
        $this->updateOrderService = $updateOrder;
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
        $order = $args['order'];

        try {
            $data = new UpdateOrderData(
                $orderId,
                $order['addressOne'] ?? null,
                $order['addressTwo'] ?? null,
                $order['addressThree'] ?? null,
                $order['city'] ?? null,
                $order['region'] ?? null,
                $order['postalCode'] ?? null,
                $order['country'] ?? null
            );
            $this->updateOrderService->updateOrder($data);
            $context->stores()->addSingle($uuid, $info);
            return $this->stores->getSingleStore($uuid, $context);
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $e);
        }
    }
}
