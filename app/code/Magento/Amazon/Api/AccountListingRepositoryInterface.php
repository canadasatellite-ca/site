<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface AccountListingRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AccountListingRepositoryInterface
{
    /**
     * Create and/or update account settings
     *
     * @param AccountListingInterface $account
     * @return AccountListingInterface
     * @throws CouldNotSaveException
     */
    public function save(AccountListingInterface $account);

    /**
     * Get account object by account id
     *
     * @param int $merchantId
     * @return AccountListingInterface
     */
    public function getByMerchantId($merchantId);
}
