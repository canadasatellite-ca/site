<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$productRepository = $objectManager->get(\Magento\Catalog\Model\ProductRepository::class);
$product1 = $objectManager->create(\Magento\Catalog\Model\Product::class);
$product1->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Isle of Dogs Coature No. 16 Dog Shampoo, 1 liter')
    ->setSku('B002LAS6YE')
    ->setPrice(45.49)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0]);
$productRepository->save($product1);

$product2 = $objectManager->create(\Magento\Catalog\Model\Product::class);
$product2->setTypeId('simple')
    ->setId(2)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Isle of Dogs Coature No. 18 Dog Shampoo, 1 liter')
    ->setSku('B002LAS6YY')
    ->setPrice(47.76)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0]);
$productRepository->save($product2);

$product3 = $objectManager->create(\Magento\Catalog\Model\Product::class);
$product3->setTypeId('simple')
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Isle of Dogs Coature No. 20 Dog Shampoo, 1 liter')
    ->setSku('B002LAS6Z8')
    ->setPrice(50.93)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0]);
$productRepository->save($product3);
