<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\AccountInterface;

/**
 * Interface AccountListingRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AccountManagementInterface
{
    /**
     * @param AccountInterface $account
     * @return bool
     */
    public function isAccountReadyToPushCommands(AccountInterface $account): bool;
}
