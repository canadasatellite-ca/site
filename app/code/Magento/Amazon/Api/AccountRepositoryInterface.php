<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface AccountRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface AccountRepositoryInterface
{
    /**
     * Create and/or update account settings
     *
     * @param AccountInterface $account
     * @return AccountInterface
     * @throws CouldNotSaveException
     */
    public function save(AccountInterface $account);

    /**
     * Delete account
     *
     * @param AccountInterface $account
     * @return void
     */
    public function delete(AccountInterface $account);

    /**
     * Get account object by account id
     *
     * @param int $merchantId
     * @param boolean $empty
     * @return AccountInterface
     * @throws NoSuchEntityException
     */
    public function getByMerchantId($merchantId, $empty = false);

    /**
     * Get account object by account uuid
     *
     * @param string $uuid
     * @return AccountInterface
     * @throws NoSuchEntityException
     */
    public function getByUuid(string $uuid);

    /**
     * Checks for an existing account
     *
     * @return bool
     */
    public function isAccount();
}
