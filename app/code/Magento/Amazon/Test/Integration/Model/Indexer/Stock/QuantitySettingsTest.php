<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Test\Integration\Model\Indexer;

require_once __DIR__ . '/StockTestCase.php';

/**
 * @magentoDbIsolation  disabled
 * @magentoAppIsolation enabled
 */
class QuantitySettingsTest extends StockTestCase
{
    /**
     * CHAN-86
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testOutOfStockThreshold()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMinQty(10)
            ->setMaxQty(1000);
        $accountListingRepository->save($accountListing);

        // Quantity in range
        $productRepository = $this->objectManager->get(
            \Magento\Catalog\Model\ProductRepository::class
        );
        $product = $productRepository->getById(1);
        $product->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 100,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(100, $this->getQuantityBySku($listings, 'B002LAS6YE'));

        // Quantity out of range
        $product = $productRepository->getById(1);
        $product->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 9,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(0, $this->getQuantityBySku($listings, 'B002LAS6YE'));
    }

    /**
     * CHAN-87
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testMaximumListedQuantity()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMinQty(0)
            ->setMaxQty(15);
        $accountListingRepository->save($accountListing);

        // Quantity in range
        $productRepository = $this->objectManager->get(
            \Magento\Catalog\Model\ProductRepository::class
        );
        $product = $productRepository->getById(1);
        $product->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 14,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(14, $this->getQuantityBySku($listings, 'B002LAS6YE'));

        // Quantity out of range
        $product = $productRepository->getById(1);
        $product->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 20,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(15, $this->getQuantityBySku($listings, 'B002LAS6YE'));
    }

    /**
     * CHAN-88
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testMaximumListedQuantityLessThanOutOfStockThreshold()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMinQty(5)
            ->setMaxQty(1);
        $accountListingRepository->save($accountListing);

        $productRepository = $this->objectManager->get(
            \Magento\Catalog\Model\ProductRepository::class
        );
        $product = $productRepository->getById(1);
        $product->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 10,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(1, $this->getQuantityBySku($listings, 'B002LAS6YE'));
    }

    /**
     * CHAN-89
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testDoNotManageStockQuantity()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMinQty(0)
            ->setMaxQty(1000)
            ->setCustomQty(200);
        $accountListingRepository->save($accountListing);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(200, $this->getQuantityBySku($listings, 'B002LAS6YE'));
    }

    /**
     * Do Not Manage Stock Quantity greater than Maximum Listed Quantity
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testDoNotManageStockQuantityGreaterThanMaximumListedQuantity()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMinQty(0)
            ->setMaxQty(50)
            ->setCustomQty(200);
        $accountListingRepository->save($accountListing);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(50, $this->getQuantityBySku($listings, 'B002LAS6YE'));
    }

    /**
     * CHAN-90
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_shared_seller_sku.php
     */
    public function testSharedSellerSku()
    {
        $this->markTestIncomplete('Figure out the reason of test failure in scope of CHAN-4478');
        $productRepository = $this->objectManager->get(
            \Magento\Catalog\Model\ProductRepository::class
        );
        $account1 = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing1 = $accountListingRepository->getByMerchantId($account1->getMerchantId());
        $accountListing1->setMinQty(10)
            ->setMaxQty(100)
            ->setCustomQty(50);
        $accountListingRepository->save($accountListing1);

        $account2 = $this->getAccountByName('mage-test-mx');
        $accountListing2 = $accountListingRepository->getByMerchantId($account2->getMerchantId());
        $accountListing2->setMinQty(5)
            ->setMaxQty(200)
            ->setCustomQty(100);
        $accountListingRepository->save($accountListing2);

        // Do Not Manage Stock
        $this->indexer->executeFull();

        $listings1 = $this->getListingsByAccount($account1);
        $listings2 = $this->getListingsByAccount($account2);

        // Shared Seller SKU
        $this->assertEquals(50, $this->getQuantityBySku($listings1, 'B002LAS6YE'));
        $this->assertEquals(50, $this->getQuantityBySku($listings2, 'B002LAS6YE'));

        // Non-shared Seller SKU
        $this->assertEquals(50, $this->getQuantityBySku($listings1, 'B002LAS6YY'));
        $this->assertEquals(100, $this->getQuantityBySku($listings2, 'B002LAS6YY'));

        // Quantity Less Than Min Quantity for Priority Market
        $product1 = $productRepository->getById(1);
        $product1->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 6,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product1);
        $product2 = $productRepository->getById(2);
        $product2->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 6,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product2);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings1 = $this->getListingsByAccount($account1);
        $listings2 = $this->getListingsByAccount($account2);

        // Shared Seller SKU
        $this->assertEquals(0, $this->getQuantityBySku($listings1, 'B002LAS6YE'));
        $this->assertEquals(0, $this->getQuantityBySku($listings2, 'B002LAS6YE'));

        // Non-shared Seller SKU
        $this->assertEquals(0, $this->getQuantityBySku($listings1, 'B002LAS6YY'));
        $this->assertEquals(6, $this->getQuantityBySku($listings2, 'B002LAS6YY'));

        // Quantity Greater Than Max for Priority Market
        $product1 = $productRepository->getById(1);
        $product1->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 150,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product1);
        $product2 = $productRepository->getById(2);
        $product2->setStockData(
            [
                'use_config_manage_stock' => 1,
                'qty' => 150,
                'is_qty_decimal' => 0,
                'is_in_stock' => 1
            ]
        );
        $productRepository->save($product2);
        $this->reindexCatalogInventoryStock();

        $this->indexer->executeFull();

        $listings1 = $this->getListingsByAccount($account1);
        $listings2 = $this->getListingsByAccount($account2);

        // Shared Seller SKU
        $this->assertEquals(100, $this->getQuantityBySku($listings1, 'B002LAS6YE'));
        $this->assertEquals(100, $this->getQuantityBySku($listings2, 'B002LAS6YE'));

        // Non-shared Seller SKU
        $this->assertEquals(100, $this->getQuantityBySku($listings1, 'B002LAS6YY'));
        $this->assertEquals(150, $this->getQuantityBySku($listings2, 'B002LAS6YY'));
    }

    protected function reindexCatalogInventoryStock()
    {
        $modelIndexer = $this->objectManager->create(
            \Magento\Indexer\Model\Indexer::class
        );
        $modelIndexer->load('cataloginventory_stock');
        $modelIndexer->reindexAll();
    }
}
