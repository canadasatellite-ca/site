<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Amazon\Api\Data\AccountListingInterfaceFactory;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Api\Data\WebsiteInterface;

class CreateAccountListingSettings
{
    /**
     * @var AccountListingRepositoryInterface
     */
    private $accountListingRepository;
    /**
     * @var AccountListingInterfaceFactory
     */
    private $accountListingFactory;
    /**
     * @var AscClientLogger
     */
    private $logger;

    /**
     * SaveSearchMappings constructor.
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param AccountListingInterfaceFactory $accountListingFactory
     * @param AscClientLogger $logger
     */
    public function __construct(
        AccountListingRepositoryInterface $accountListingRepository,
        AccountListingInterfaceFactory $accountListingFactory,
        AscClientLogger $logger
    ) {
        $this->accountListingRepository = $accountListingRepository;
        $this->accountListingFactory = $accountListingFactory;
        $this->logger = $logger;
    }

    /**
     * @param AccountInterface $account
     * @param WebsiteInterface $website
     * @param SearchMappingData $data
     * @throws CouldNotSaveException
     */
    public function createForAccount(
        AccountInterface $account,
        SearchMappingData $data
    ): void {
        /** @var AccountListingInterface $accountListing */
        $accountListing = $this->accountListingFactory->create();

        $accountListing->setMerchantId($account->getMerchantId());
        $amazonIdType = $data->getAmazonIdType();

        if ($amazonIdType === 'asin') {
            $accountListing->setAsinMappingField($data->getMagentoAttributeCode());
        }
        if ($amazonIdType === 'ean') {
            $accountListing->setEanMappingField($data->getMagentoAttributeCode());
        }
        if ($amazonIdType === 'gcid') {
            $accountListing->setGcidMappingField($data->getMagentoAttributeCode());
        }
        if ($amazonIdType === 'isbn') {
            $accountListing->setIsbnMappingField($data->getMagentoAttributeCode());
        }
        if ($amazonIdType === 'upc') {
            $accountListing->setUpcMappingField($data->getMagentoAttributeCode());
        }
        if ($amazonIdType === 'general') {
            $accountListing->setGeneralMappingField($data->getMagentoAttributeCode());
        }

        // import third party listings by default at account creation
        $accountListing->setThirdpartyIsActive(1);
        $accountListing->setThirdpartySkuField('sku');
        $accountListing->setThirdpartyAsinField(0);
        // this is very optimistic assumption that the price field has the default name
        $accountListing->setPriceField('price');

        try {
            $this->accountListingRepository->save($accountListing);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical('An error occurred while saving account listing settings', ['exception' => $e]);
            throw $e;
        }
    }
}
