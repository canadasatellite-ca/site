<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$allowedCurrenciesPath = 'currency/options/allow';
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$configResource = $objectManager->create(\Magento\Config\Model\ResourceModel\Config::class);
$configResource->saveConfig(
    $allowedCurrenciesPath,
    'AUD,USD',
    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
    0
);
$currency = $objectManager->create(\Magento\Directory\Model\ResourceModel\Currency::class);
$currency->saveRates(
    [
    'AUD' => ['USD' => 1],
    'USD' => ['AUD' => 1.324070]
    ]
);
$objectManager->get(\Magento\Framework\App\Config\ReinitableConfigInterface::class)->reinit();
$objectManager->create(\Magento\Store\Model\StoreManagerInterface::class)->reinitStores();
