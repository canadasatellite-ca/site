<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\Data\OrderTrackingInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Model\Order\Shipment\Item as ShipmentItem;

/**
 * Interface OrderTrackingManagementInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderTrackingManagementInterface
{
    /**
     * Inserts new shipment track into custom
     * order table and marks the order as "shipped"
     * (if applicable)
     *
     * @param ShipmentTrackInterface $track
     * @param OrderInterface $marketplaceOrder
     * @param ShipmentItem $orderItem
     * @return OrderTrackingInterface
     * @throws CouldNotSaveException
     */
    public function insert(
        ShipmentTrackInterface $track,
        OrderInterface $marketplaceOrder,
        ShipmentItem $orderItem
    );
}
