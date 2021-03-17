<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listing_rule.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$lowestRepository = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Lowest::class
);
$lowest1[] = [
    'asin' => 'B002LAS6YY',
    'country_code' => 'US',
    'condition_code' => 11,
    'condition' => 'New',
    'subcondition' => 'New',
    'fulfillment_channel' => 'Amazon',
    'feedback_rating' => 98,
    'feedback_count' => 159701,
    'currency_code' => 'USD',
    'landed_price' => 57.77,
    'list_price' => 57.77,
    'shipping_price' => 0
];
$lowestRepository->insert($lowest1);
$lowest2[] = [
    'asin' => 'B002LAS6YY',
    'country_code' => 'US',
    'condition_code' => 11,
    'condition' => 'New',
    'subcondition' => 'New',
    'fulfillment_channel' => 'Merchant',
    'feedback_rating' => 98,
    'feedback_count' => 161258,
    'currency_code' => 'USD',
    'landed_price' => 50.91,
    'list_price' => 50.91,
    'shipping_price' => 0
];
$lowestRepository->insert($lowest2);

$bestBuyBoxRepository = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Bestbuybox::class
);
$bestBuy[] = [
    'asin' => 'B002LAS6YY',
    'is_seller' => 0,
    'country_code' => 'US',
    'condition_code' => 11,
    'condition' => 'New',
    'subcondition' => 'New',
    'currency_code' => 'USD',
    'landed_price' => 50.90,
    'list_price' => 50.90,
    'shipping_price' => 0
];
$bestBuyBoxRepository->insert($bestBuy);
