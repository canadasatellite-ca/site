<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$accountCollectionFactory = $objectManager->create(
    Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
);
$account = $accountCollectionFactory->create()
    ->addFieldToFilter('name', 'mage-test')
    ->getFirstItem();
if ($account->getMerchantId() === null) {
    $accountRepository = $objectManager->get(\Magento\Amazon\Model\Amazon\AccountRepository::class);
    $account = $objectManager->create(\Magento\Amazon\Model\Amazon\Account::class);
    $account->setSetupStep(0)
        ->setIsActive(1)
        ->setSellerId('AAAAABBBBBCCC')
        ->setSignature('')
        ->setName('mage-test')
        ->setEmail('test@test.com')
        ->setBaseUrl('http://magento2.vagrant100/')
        ->setConsumerKey('')
        ->setConsumerSecret('')
        ->setAccessToken('')
        ->setAccessSecret('')
        ->setUuid('ffb078fd-6717-4385-b1b4-851dd03e58fc');
    $account->setCountryCode('US');
    $accountRepository->save($account, 0);

    $accountListingRepository = $objectManager->get(\Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class);
    $accountListing = $objectManager->create(\Magento\Amazon\Model\Amazon\Account\Listing::class);
    $accountListing->setMerchantId($account->getMerchantId())
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
    $accountListingRepository->save($accountListing);
}
