<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\WebsiteInterface;

class CreateAccount
{
    /**
     * @var \Magento\Amazon\Model\ApiClient
     */
    private $apiClient;
    /**
     * @var \Magento\Amazon\Api\AccountRepositoryInterface
     */
    private $accountRepository;
    /**
     * @var \Magento\Amazon\Api\Data\AccountInterfaceFactory
     */
    private $accountFactory;
    /**
     * @var \Magento\Amazon\Logger\AscClientLogger
     */
    private $logger;

    /**
     * @var array Website Code to URL mapping
     */
    private $websiteUrls = [];
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CreateAccountListingRule
     */
    private $createAccountListingRule;
    /**
     * @var CreateAccountListingSettings
     */
    private $createAccountListingSettings;
    /**
     * @var CreateAccountOrderSettings
     */
    private $createAccountOrderSettings;

    public function __construct(
        \Magento\Amazon\Model\ApiClient $apiClient,
        \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository,
        \Magento\Amazon\Api\Data\AccountInterfaceFactory $accountFactory,
        \Magento\Amazon\Logger\AscClientLogger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CreateAccountListingRule $createAccountListingRule,
        CreateAccountListingSettings $createAccountListingSettings,
        CreateAccountOrderSettings $createAccountOrderSettings
    ) {
        $this->apiClient = $apiClient;
        $this->accountRepository = $accountRepository;
        $this->accountFactory = $accountFactory;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->createAccountListingRule = $createAccountListingRule;
        $this->createAccountListingSettings = $createAccountListingSettings;
        $this->createAccountOrderSettings = $createAccountOrderSettings;
    }

    /**
     * @param CreateAccountData $data
     * @return AccountInterface
     * @throws \Magento\Amazon\Model\ApiClient\ApiException
     * @throws \Magento\Amazon\Model\ApiClient\ResponseValidationException
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function createAccount(CreateAccountData $data, SearchMappingData $searchMappingData): AccountInterface
    {
        \Assert\Assertion::inArray(
            $data->getWebsiteCode(),
            array_keys($this->getAllWebsites()),
            'Unknown website code'
        );
        $response = $this->createRemoteAccount($data);
        $uuid = $response['uuid'];
        try {
            $account = $this->createLocalAccount($uuid, $data);
            $website = $this->getWebsite($data);
            $this->createAccountListingRule->createForAccount($account, $website);
            $this->createAccountListingSettings->createForAccount($account, $searchMappingData);
            $this->createAccountOrderSettings->createForAccount($account, $website);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical('Exception occurred during saving account', ['exception' => $e]);
            try {
                $this->apiClient->deleteMerchant($uuid, $data->getCountryCode());
            } catch (\Exception $exception) {
                $this->logger->critical('Exception occurred during fallback account deletion', ['exception' => $e]);
            }
            throw $e;
        }
        return $account;
    }

    /**
     * @return WebsiteInterface[]
     */
    private function getAllWebsites(): array
    {
        return $this->storeManager->getWebsites(true, true);
    }

    /**
     * @param CreateAccountData $data
     * @return array
     * @throws \Magento\Amazon\Model\ApiClient\ApiException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    private function createRemoteAccount(CreateAccountData $data): array
    {
        $merchantProfile = [
            'country_code' => $data->getCountryCode(),
            'email' => $data->getEmail(),
            'name' => $data->getName(),
            'base_url' => $this->getWebsiteUrl($data),
        ];

        return $this->apiClient->createMerchant($merchantProfile);
    }

    /**
     * @param CreateAccountData $data
     * @return string|null
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getWebsiteUrl(CreateAccountData $data): ?string
    {
        $websiteCode = $data->getWebsiteCode();
        if (!array_key_exists($websiteCode, $this->websiteUrls)) {
            /** @var \Magento\Store\Model\Website[] $websites */
            $websites = $this->getAllWebsites();
            $website = $websites[$websiteCode] ?? null;
            if (null === $website) {
                throw new LocalizedException(__("Website with code {$websiteCode} does not exist"));
            }
            $baseUrl = $website ? $website->getDefaultStore()->getBaseUrl() : null;
            if (!$baseUrl) {
                $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            }
            $this->websiteUrls[$websiteCode] = $baseUrl;
        }
        return $this->websiteUrls[$websiteCode];
    }

    /**
     * Adds credentials for API integration
     *
     * @param string $uuid
     * @param CreateAccountData $data
     * @return AccountInterface
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    private function createLocalAccount(
        string $uuid,
        CreateAccountData $data
    ): AccountInterface {
        /** @var \Magento\Amazon\Model\Amazon\Account $account */
        $account = $this->accountFactory->create();

        $account->setUuid($uuid);
        $account->setName($data->getName());
        $account->setEmail($data->getEmail());
        $account->setCountryCode($data->getCountryCode());
        $account->setBaseUrl($this->getWebsiteUrl($data));
        $account->setIsActive(Definitions::ACCOUNT_STATUS_INCOMPLETE);

        $this->accountRepository->save($account);
        return $account;
    }

    private function getWebsite(CreateAccountData $data): WebsiteInterface
    {
        $allWebsites = $this->getAllWebsites();
        return $allWebsites[$data->getWebsiteCode()] ?? $allWebsites['admin'];
    }
}
