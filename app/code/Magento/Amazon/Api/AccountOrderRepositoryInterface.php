<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Interface AccountOrderRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AccountOrderRepositoryInterface
{
    /**
     * Create and/or update account settings
     *
     * @param AccountOrderInterface $account
     * @return AccountOrderInterface
     * @throws CouldNotSaveException
     */
    public function save(AccountOrderInterface $account);

    /**
     * Get account object by account id
     *
     * @param int $merchantId
     * @return AccountOrderInterface
     */
    public function getByMerchantId($merchantId);
}
