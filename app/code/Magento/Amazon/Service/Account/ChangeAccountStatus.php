<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Indexer\PricingProcessor;
use Magento\Amazon\Model\Indexer\StockProcessor;
use Magento\Framework\Exception\LocalizedException;

class ChangeAccountStatus
{
    /**
     * @var \Magento\Amazon\Model\Amazon\AccountRepository
     */
    private $accountRepository;
    /**
     * @var \Magento\Amazon\Model\ApiClient
     */
    private $apiClient;
    /**
     * @var StockProcessor
     */
    private $stockProcessor;
    /**
     * @var PricingProcessor
     */
    private $pricingProcessor;

    public function __construct(
        \Magento\Amazon\Model\Amazon\AccountRepository $accountRepository,
        \Magento\Amazon\Model\ApiClient $apiClient,
        StockProcessor $stockProcessor,
        PricingProcessor $pricingProcessor
    ) {
        $this->accountRepository = $accountRepository;
        $this->apiClient = $apiClient;
        $this->stockProcessor = $stockProcessor;
        $this->pricingProcessor = $pricingProcessor;
    }

    public function activateByUuid(string $uuid): void
    {
        $account = $this->accountRepository->getByUuid($uuid);
        $this->activateByAccount($account);
    }

    public function activateByAccount(\Magento\Amazon\Api\Data\AccountInterface $account): void
    {
        if ((int)$account->getIsActive() === Definitions::ACCOUNT_STATUS_INCOMPLETE) {
            throw new LocalizedException(__('Cannot activate store that didn\'t finish store setup'));
        }
        $this->apiClient->updateMerchantStatus($account, 1);
        $account->setIsActive(Definitions::ACCOUNT_STATUS_ACTIVE);
        $this->accountRepository->save($account);
        $this->invalidateIndexers();
    }

    private function invalidateIndexers(): void
    {
        $this->pricingProcessor->updateMode();
        $this->pricingProcessor->getIndexer()->invalidate();
        $this->invalidateStockIndexer();
    }

    /**
     * Activates incomplete store.
     * Calling this function is an acknowledgement that the store is in potentially invalid state
     * for the activation and the responsibility for validating this is on the client of this method
     *
     * @param \Magento\Amazon\Api\Data\AccountInterface $account
     * @throws \Magento\Amazon\Model\ApiClient\ApiException
     * @throws \Magento\Amazon\Model\ApiClient\ResponseValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function activateIncompleteStoreByAccount(\Magento\Amazon\Api\Data\AccountInterface $account): void
    {
        $this->apiClient->updateMerchantStatus($account, 1);
        $account->setIsActive(Definitions::ACCOUNT_STATUS_ACTIVE);
        $this->accountRepository->save($account);
        $this->invalidateStockIndexer();
    }

    public function deactivateByUuid(string $uuid): void
    {
        $account = $this->accountRepository->getByUuid($uuid);
        $this->deactivateByAccount($account);
    }

    public function deactivateByAccount(\Magento\Amazon\Api\Data\AccountInterface $account): void
    {
        if ((int)$account->getIsActive() === Definitions::ACCOUNT_STATUS_INCOMPLETE) {
            throw new LocalizedException(__('Cannot deactivate store that didn\'t finish store setup'));
        }
        $this->apiClient->updateMerchantStatus($account, 0);
        $account->setIsActive(Definitions::ACCOUNT_STATUS_INACTIVE);
        $this->accountRepository->save($account);
        $this->invalidateIndexers();
    }

    private function invalidateStockIndexer(): void
    {
        $this->stockProcessor->updateMode();
        $this->stockProcessor->getIndexer()->invalidate();
    }
}
