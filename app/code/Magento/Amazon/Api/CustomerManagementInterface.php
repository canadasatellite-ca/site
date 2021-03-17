<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Model\Store;

/**
 * Interface CustomerManagementInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface CustomerManagementInterface
{
    /**
     * Create customer account (if applicable)
     *
     * @param OrderInterface $marketplaceOrder
     * @param Store $store
     * @return CustomerInterface
     */
    public function create(OrderInterface $marketplaceOrder, Store $store);
}
