<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\OrderTrackingInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface OrderTrackingRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderTrackingRepositoryInterface
{
    /**
     * Create and/or update amazon order
     *
     * @param OrderTrackingInterface $tracking
     * @return OrderTrackingInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderTrackingInterface $tracking);
}
