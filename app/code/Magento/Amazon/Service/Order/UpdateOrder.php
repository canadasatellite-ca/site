<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Order;

use Magento\Amazon\Api\OrderRepositoryInterface;

class UpdateOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * UpdateOrder constructor.
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param UpdateOrderData $data
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateOrder(UpdateOrderData $data): void
    {
        $order = $this->orderRepository->getOrderByMarketplaceOrderId($data->getOrderId());
        if ($data->getAddressOne() !== null) {
            $order->setShipAddressOne($data->getAddressOne());
        }
        if ($data->getAddressTwo() !== null) {
            $order->setShipAddressTwo($data->getAddressTwo());
        }
        if ($data->getAddressThree() !== null) {
            $order->setShipAddressThree($data->getAddressThree());
        }
        if ($data->getCity() !== null) {
            $order->setShipCity($data->getCity());
        }
        if ($data->getRegion() !== null) {
            $order->setShipRegion($data->getRegion());
        }
        if ($data->getPostalCode() !== null) {
            $order->setShipPostalCode($data->getPostalCode());
        }
        if ($data->getCountry() !== null) {
            $order->setShipCountry($data->getCountry());
        }
        $this->orderRepository->save($order);
    }
}
