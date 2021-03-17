<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listing_rule_rollback.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$eavSetupFactory = $objectManager->create(\Magento\Eav\Setup\EavSetupFactory::class);
$eavSetup = $eavSetupFactory->create();
$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'amazon_price');
