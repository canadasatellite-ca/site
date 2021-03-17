<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Model\ApiClient;

class UpdateAccount
{
    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * UpdateAccount constructor.
     * @param AccountRepositoryInterface $accountRepository
     * @param ApiClient $apiClient
     */
    public function __construct(
        AccountRepositoryInterface $accountRepository,
        ApiClient $apiClient
    ) {
        $this->accountRepository = $accountRepository;
        $this->apiClient = $apiClient;
    }

    /**
     * @param UpdateAccountData $data
     * @throws ApiClient\ApiException
     * @throws ApiClient\ResponseFormatValidationException
     * @throws ApiClient\ResponseValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateAccount(UpdateAccountData $data): void
    {
        $account = $this->accountRepository->getByUuid($data->getUuid());
        $fields = [];
        if ($data->getEmail() !== null) {
            $account->setEmail($data->getEmail());
            $fields['email'] = $data->getEmail();
        }
        if ($data->getName() !== null) {
            $account->setName($data->getName());
            $fields['name'] = $data->getName();
        }
        $this->accountRepository->save($account);
        $this->apiClient->updateMerchant($account, $fields);
    }
}
