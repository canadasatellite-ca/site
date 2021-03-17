<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listing_rule.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$listingCollectionFactory = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory::class
);
$listings = $listingCollectionFactory->create()
    ->addFieldToFilter('merchant_id', $account->getMerchantId()); /** @phpstan-ignore-line */
$listingRepository = $objectManager->get(
    \Magento\Amazon\Model\Amazon\ListingRepository::class
);
foreach ($listings as $listing) {
    $listing->setIsShip(0);
    $listingRepository->save($listing);
}
