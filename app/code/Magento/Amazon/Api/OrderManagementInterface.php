<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Interface OrderManagementInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderManagementInterface
{
    /**
     * Obtains order revenue over a 30 day period
     * by merchant id
     *
     * @param int $merchantId
     * @return array
     */
    public function getRevenueByMerchantId($merchantId);

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function canBuildOrder(OrderInterface $order): bool;

    /**
     * Create magento order for order imported from Amazon
     *
     * @param OrderInterface $amazonOrder
     * @param AccountOrderInterface $accountOrder
     * @param bool $stockReserved
     * @return bool|OrderInterface
     */
    public function create(OrderInterface $amazonOrder, AccountOrderInterface $accountOrder, bool $stockReserved);

    /**
     * Get store into which order will be imported
     *
     * @param AccountOrderInterface $account
     * @return StoreInterface
     */
    public function getStoreForOrder(AccountOrderInterface $account): StoreInterface;

    /**
     * Set Amazon order to completed status
     *
     * @param OrderInterface $order
     * @param bool $completeShipment
     * @return void
     */
    public function setCompleted(OrderInterface $order, bool $completeShipment);

    /**
     * Sets order notes
     *
     * @param OrderInterface $order
     * @param string $notes
     * @return void
     */
    public function setOrderNotes(OrderInterface $order, string $notes);

    /**
     * Refunds and closes order
     *
     * @param int $orderId
     * @return bool
     */
    public function refundOrder(int $orderId): bool;
}
