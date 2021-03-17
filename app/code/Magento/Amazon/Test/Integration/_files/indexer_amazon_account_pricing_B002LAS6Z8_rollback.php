<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listing_rule_rollback.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$lowestRepository = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Lowest::class
);
$lowestRepository->removeByAsins(['AAAAA99999000' => ['B002LAS6Z8']]);

$bestBuyBoxRepository = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Bestbuybox::class
);
$bestBuyBoxRepository->removeByAsins(['AAAAA99999000' => ['B002LAS6Z8']]);
