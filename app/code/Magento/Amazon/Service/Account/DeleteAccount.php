<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ApiClient;
use Magento\Amazon\Model\ApiClient\ApiException;
use Magento\Amazon\Model\ApiClient\ResponseValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class DeleteAccount
{
    /**
     * @var ApiClient
     */
    private $apiClient;
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    public function __construct(
        ApiClient $apiClient,
        AccountRepositoryInterface $accountRepository
    ) {
        $this->apiClient = $apiClient;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @param string $uuid
     * @return void
     * @throws CouldNotSaveException
     * @throws ApiException
     * @throws ResponseValidationException
     * @throws NoSuchEntityException
     */
    public function deleteAccount(string $uuid): void
    {
        try {
            $account = $this->getLocalAccount($uuid);
            $this->deleteRemoteAccount($account);
            $this->deleteLocalAccount($account);
        } catch (NoSuchEntityException $e) {
            // store already deleted
        }
    }

    /**
     * @param string $uuid
     * @return AccountInterface
     * @throws NoSuchEntityException
     */
    private function getLocalAccount(string $uuid): AccountInterface
    {
        return $this->accountRepository->getByUuid($uuid);
    }

    /**
     * @param AccountInterface $account
     * @return void
     * @throws ApiException
     * @throws ResponseValidationException
     */
    private function deleteRemoteAccount(AccountInterface $account): void
    {
        $this->apiClient->deleteMerchant($account->getUuid(), $account->getCountryCode());
    }

    /**
     * Delete local account
     *
     * @param AccountInterface $account
     * @return void
     */
    private function deleteLocalAccount(AccountInterface $account): void
    {
        $this->accountRepository->delete($account);
    }
}
