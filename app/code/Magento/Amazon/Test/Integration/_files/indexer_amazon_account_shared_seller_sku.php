<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listing_rule.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$accountRepository = $objectManager->get(\Magento\Amazon\Model\Amazon\AccountRepository::class);
$account1 = $objectManager->create(\Magento\Amazon\Model\Amazon\Account::class);
$account1->setSetupStep(0)
    ->setIsActive(1)
    ->setSellerId('AAAAABBBBBCCC')
    ->setSignature('')
    ->setName('mage-test-mx')
    ->setEmail('test_mx@test.com')
    ->setBaseUrl('http://magento2.vagrant101/')
    ->setConsumerKey('')
    ->setConsumerSecret('')
    ->setAccessToken('')
    ->setAccessSecret('')
    ->setUuid('a057424d-1ee9-4af9-845f-cd811b723eea');
$account1->setCountryCode('MX');
$accountRepository->save($account1, 0);

$accountListingRepository = $objectManager->get(\Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class);
$accountListing1 = $objectManager->create(\Magento\Amazon\Model\Amazon\Account\Listing::class);
$accountListing1->setMerchantId($account1->getMerchantId())
    ->setAutoList(false)
    ->setThirdpartyIsActive(0)
    ->setHandlingTime(2)
    ->setListCondition(11)
    ->setFulfilledBy(1)
    ->setCustomQty(10)
    ->setMinQty(0)
    ->setMaxQty(10)
    ->setVatIsActive(0)
    ->setVatPercentage(0.000)
    ->setCcIsActive(0)
    ->setPriceField('price')
    ->setGeneralMappingField('name');
$accountListingRepository->save($accountListing1);

$listingManagement = $objectManager->create(
    \Magento\Amazon\Model\Amazon\ListingManagement::class
);
$listingManagement->insertByProductIds([1, 2, 3], $account1->getMerchantId());

$listingRepository = $objectManager->create(
    \Magento\Amazon\Model\Amazon\ListingRepository::class
);
$listingCollectionFactory = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory::class
);
$listings = $listingCollectionFactory->create()
    ->addFieldToFilter('merchant_id', $account1->getMerchantId());
foreach ($listings as $listing) {
    $listing->setListStatus(\Magento\Amazon\Model\Amazon\Definitions::ERROR_LIST_STATUS)
        ->setAsin($listing->getCatalogSku())
        ->setIsShip(1);
    $listingRepository->save($listing);
}

// Set MX account listing 'B002LAS6YE' to same Amazon SKU
$listings = $listingCollectionFactory->create()
    ->addFieldToFilter('merchant_id', $account1->getMerchantId());
$listing = $listings->getItemByColumnValue('catalog_sku', 'B002LAS6YE');
$listing->setAsin($listing->getCatalogSku())
    ->setSellerSku($listing->getCatalogSku());
$listingRepository->save($listing);

$listingRuleRepository = $objectManager->get(
    \Magento\Amazon\Model\Amazon\Listing\ListingRuleRepository::class
);
$listingRule = $objectManager->create(
    \Magento\Amazon\Model\Amazon\Listing\Rule::class
);
$listingRule->setMerchantId($account1->getMerchantId())
    ->setConditionsSerialized(
        file_get_contents(__DIR__ . '/pricing_rule_empty_condition.json')
    );
$listingRule->setWebsiteId(1);
$listingRuleRepository->save($listingRule);

// Set NA account listing 'B002LAS6YE' to same Amazon SKU
$accountCollectionFactory = $objectManager->create(
    Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
);
$account2 = $accountCollectionFactory->create()
    ->addFieldToFilter('name', 'mage-test')
    ->getFirstItem();
$listings = $listingCollectionFactory->create()
    ->addFieldToFilter('merchant_id', $account2->getMerchantId());
$listing = $listings->getItemByColumnValue('catalog_sku', 'B002LAS6YE');
$listing->setAsin($listing->getCatalogSku())
    ->setSellerSku($listing->getCatalogSku());
$listingRepository->save($listing);
