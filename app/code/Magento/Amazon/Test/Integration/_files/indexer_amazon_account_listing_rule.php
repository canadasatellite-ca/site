<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listings.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$accountCollectionFactory = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
);
$account = $accountCollectionFactory->create()
    ->addFieldToFilter('name', 'mage-test')
    ->getFirstItem();
$merchantId = $account->getMerchantId();
$listingRuleRepository = $objectManager->get(
    \Magento\Amazon\Model\Amazon\Listing\ListingRuleRepository::class
);
$existingListingRule = $listingRuleRepository->getByMerchantId($merchantId);
if ($existingListingRule->getId() === null) {
    $listingRule = $objectManager->create(
        \Magento\Amazon\Model\Amazon\Listing\Rule::class
    );
    $listingRule->setMerchantId($merchantId)
        ->setConditionsSerialized(
            file_get_contents(__DIR__ . '/pricing_rule_empty_condition.json')
        );
    $listingRule->setWebsiteId(1);
    $listingRuleRepository->save($listingRule);
}
