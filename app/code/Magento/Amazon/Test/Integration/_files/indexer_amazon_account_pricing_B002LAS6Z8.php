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
    'asin' => 'B002LAS6Z8',
    'country_code' => 'US',
    'condition_code' => 11,
    'condition' => 'New',
    'subcondition' => 'New',
    'fulfillment_channel' => 'Merchant',
    'feedback_rating' => 98,
    'feedback_count' => 2647,
    'currency_code' => 'USD',
    'landed_price' => 61.80,
    'list_price' => 61.80,
    'shipping_price' => 0,
];
$lowestRepository->insert($lowest1);

$lowest2[] = [
    'asin' => 'B002LAS6Z8',
    'country_code' => 'US',
    'condition_code' => 11,
    'condition' => 'New',
    'subcondition' => 'New',
    'fulfillment_channel' => 'Merchant',
    'feedback_rating' => 95,
    'feedback_count' => 205,
    'currency_code' => 'USD',
    'landed_price' => 59.80,
    'list_price' => 59.80,
    'shipping_price' => 0,
];
$lowestRepository->insert($lowest2);

$lowest3[] = [
    'asin' => 'B002LAS6Z8',
    'country_code' => 'US',
    'condition_code' => 2,
    'condition' => 'Used',
    'subcondition' => 'VeryGood',
    'fulfillment_channel' => 'Amazon',
    'feedback_rating' => 98,
    'feedback_count' => 159701,
    'currency_code' => 'USD',
    'landed_price' => 67.75,
    'list_price' => 67.75,
    'shipping_price' => 0,
];
$lowestRepository->insert($lowest3);

$lowest4[] = [
    'asin' => 'B002LAS6Z8',
    'country_code' => 'US',
    'condition_code' => 3,
    'condition' => 'Used',
    'subcondition' => 'Good',
    'fulfillment_channel' => 'Merchant',
    'feedback_rating' => 98,
    'feedback_count' => 161258,
    'currency_code' => 'USD',
    'landed_price' => 51.95,
    'list_price' => 51.95,
    'shipping_price' => 0,
];
$lowestRepository->insert($lowest4);

$bestBuyBoxRepository = $objectManager->create(
    \Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Bestbuybox::class
);
$bestBuy[] = [
    'asin' => 'B002LAS6Z8',
    'is_seller' => 0,
    'country_code' => 'US',
    'condition_code' => 11,
    'condition' => 'New',
    'subcondition' => 'New',
    'currency_code' => 'USD',
    'landed_price' => 51.90,
    'list_price' => 51.90,
    'shipping_price' => 0,
];
$bestBuyBoxRepository->insert($bestBuy);
