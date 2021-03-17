<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Order;

use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Api\Data\OrderInterface;

interface OrderHandlerInterface
{
    /**
     * Synchronize amazon orders to magento orders
     * and managing inventory reserves
     *
     * @param int $merchantId
     * @return void
     */
    public function synchronizeOrders(int $merchantId);

    /**
     * Cancels the Amazon order
     *
     * @param string $orderId
     * @return void
     */
    public function cancel(string $orderId);

    /**
     * Create magento order for order imported from Amazon
     *
     * @param OrderInterface $amazonOrder
     * @param AccountOrderInterface $orderSetting
     * @return bool|OrderInterface
     */
    public function createMagentoOrder(OrderInterface $amazonOrder, AccountOrderInterface $orderSetting);
}
