<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require __DIR__ . '/indexer_amazon_account_listing_rule.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$eavSetupFactory = $objectManager->create(
    \Magento\Eav\Setup\EavSetupFactory::class
);
$eavSetup = $eavSetupFactory->create();
$eavSetup->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'amazon_price',
    [
        'type' => 'decimal',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
        'frontend' => '',
        'label' => 'Amazon Price',
        'input' => 'price',
        'class' => '',
        'source' => '',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
        'apply_to' => ''
    ]
);

$productRepository = $objectManager->get(\Magento\Catalog\Model\ProductRepository::class);

foreach (['B002LAS6YE', 'B002LAS6YY', 'B002LAS6Z8'] as $sku) {
    $product = $productRepository->get($sku, false, null, true);
    $amazonPrice = $product->getPrice() + 100;
    $product->addAttributeUpdate('amazon_price', $amazonPrice, 0);
    $productRepository->save($product);
}
