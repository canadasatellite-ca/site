<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface OrderRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderRepositoryInterface
{
    /**
     * Create and/or update amazon order
     *
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $order);

    /**
     * Get order object by order id
     *
     * @param int $orderId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getByOrderId($orderId);

    /** Get order by sales order id
     *
     * @param string $orderId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getOrderBySalesOrderId($orderId);

    /**
     * Get order by marketplace order id
     *
     * @param string $orderId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getOrderByMarketplaceOrderId(string $orderId): OrderInterface;
}
