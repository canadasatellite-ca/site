<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Test\Integration\Model\Indexer;

require_once __DIR__ . '/PricingTestCase.php';

/**
 * @magentoAppIsolation enabled
 */
class PriceSettingsTest extends PricingTestCase
{
    /**
     * CHAN-70
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testGlobalPrice()
    {
        $this->indexer->executeFull();

        $account = $this->getAccountByName('mage-test');
        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(45.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(47.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-71
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_price.php
     */
    public function testCustomPriceFallBack()
    {
        // Set custom price
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setPriceField('amazon_price');
        $accountListingRepository->save($accountListing);

        // Remove product attribute
        $productRepository = $this->objectManager->get(
            \Magento\Catalog\Model\ProductRepository::class
        );
        $product = $productRepository->get('B002LAS6Z8', false, null, true);
        $product->addAttributeUpdate('amazon_price', null, 0);
        $productRepository->save($product);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(145.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(147.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-76
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testGlobalPriceVat()
    {
        // Enable VAT
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setVatIsActive(1)
            ->setVatPercentage(12.5789);
        $accountListingRepository->save($accountListing);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(51.21, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(53.77, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(57.34, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-80
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testGlobalPriceCurrencyConversion()
    {
        // Set account default marketplace to AU and currency to AUD
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(60.23, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(63.24, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(67.43, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-81
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testGlobalPriceVatCurrencyConversion()
    {
        // Enable VAT, set account default marketplace to AU and currency to AUD
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setCcIsActive(1)
            ->setCcRate('AUD')
            ->setVatIsActive(1)
            ->setVatPercentage(12.5789);
        $accountListingRepository->save($accountListing);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(67.81, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(71.19, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(75.91, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_price.php
     */
    public function testCustomPrice()
    {
        // Set custom price
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setPriceField('amazon_price');
        $accountListingRepository->save($accountListing);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(145.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(147.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(150.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-82
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_price.php
     */
    public function testCustomPriceVat()
    {
        // Set VAT and custom price
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setVatIsActive(1)
            ->setVatPercentage(9.6498)
            ->setPriceField('amazon_price');
        $accountListingRepository->save($accountListing);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(159.53, $this->getListingPriceBySku($listings, 'B002LAS6YE'));    //159.5294
        $this->assertEquals(162.02, $this->getListingPriceBySku($listings, 'B002LAS6YY'));    //162.0185
        $this->assertEquals(165.49, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));    //165.4944
    }

    /**
     * CHAN-83
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_price.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testCustomPriceCurrencyConversion()
    {
        // Set custom price and currency conversion
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setPriceField('amazon_price')
            ->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(192.64, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(195.64, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(199.84, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-84
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_price.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testCustomPriceVatCurrencyConversion()
    {
        // Set VAT, custom price and currency conversion
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setPriceField('amazon_price')
            ->setVatIsActive(1)
            ->setVatPercentage(9.6498)
            ->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(211.23, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(214.52, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(219.12, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }
}
