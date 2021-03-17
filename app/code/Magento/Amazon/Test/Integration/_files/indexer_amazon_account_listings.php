<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account.php';
require __DIR__ . '/indexer_products.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$accountCollectionFactory = $objectManager->create(
    Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
);
$account = $accountCollectionFactory->create()
    ->addFieldToFilter('name', 'mage-test')
    ->getFirstItem();
$listingCollectionFactory = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory::class
);
$existingListing = $listingCollectionFactory->create()
    ->addFieldToFilter('merchant_id', $account->getMerchantId())
    ->addFieldToFilter('catalog_sku', 'B002LAS6YE')
    ->getFirstItem();

if ($existingListing->getId() === null) {
    $listingManagement = $objectManager->create(
        \Magento\Amazon\Model\Amazon\ListingManagement::class
    );
    $listingManagement->insertByProductIds([1, 2, 3], $account->getMerchantId());

    $listings = $listingCollectionFactory->create()
        ->addFieldToFilter('merchant_id', $account->getMerchantId());
    $listingRepository = $objectManager->get(
        \Magento\Amazon\Model\Amazon\ListingRepository::class
    );
    foreach ($listings as $listing) {
        $listing->setListStatus(\Magento\Amazon\Model\Amazon\Definitions::ERROR_LIST_STATUS)
            ->setAsin($listing->getCatalogSku())
            ->setIsShip(1);
        $listingRepository->save($listing);
    }
}
