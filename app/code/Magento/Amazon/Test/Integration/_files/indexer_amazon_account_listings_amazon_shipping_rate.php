<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listing_rule.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$accountCollectionFactory = $objectManager->create(
    Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
);
$account = $accountCollectionFactory->create()
    ->addFieldToFilter('name', 'mage-test')
    ->getFirstItem();
$listingRepository = $objectManager->get(
    \Magento\Amazon\Model\Amazon\ListingRepository::class
);
$listingCollectionFactory = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory::class
);
$listings = $listingCollectionFactory->create()
    ->addFieldToFilter('merchant_id', $account->getMerchantId());
foreach ($listings as $listing) {
    $asin = $listing->getAsin();
    if ($asin == 'B002LAS6YE' || $asin == 'B002LAS6YY') {
        $listing->setShippingPrice(7);
    } else {
        $listing->setShippingPrice(5);
    }
    $listingRepository->save($listing);
}
