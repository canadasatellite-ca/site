<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\OrderItemInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface OrderItemRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface OrderItemRepositoryInterface
{
    /**
     * Get order item object by order item id
     *
     * @param int $id
     * @return OrderItemInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);
}
