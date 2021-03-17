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
class PricingRulesStandardRulesDecreaseByPercentageTest extends PricingTestCase
{
    /**
     * CHAN-185
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testGlobalPrice()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(40.94, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(42.98, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(45.84, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-186
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testGlobalPriceVat()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setVatIsActive(1)
            ->setVatPercentage(20.0000);
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(49.13, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(51.58, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(55.01, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-187
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testGlobalPriceCurrencyConversion()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMarketplace(14)
            ->setDefaultCurrency('AUD')
            ->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(54.21, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(56.92, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(60.69, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-188
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testGlobalPriceVatCurrencyConversion()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMarketplace(14)
            ->setDefaultCurrency('AUD')
            ->setVatIsActive(1)
            ->setVatPercentage(20.0000)
            ->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(65.05, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(68.30, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(72.83, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-189
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_price.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testCustomPriceVatCurrencyConversion()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setPriceField('amazon_price')
            ->setMarketplace(14)
            ->setDefaultCurrency('AUD')
            ->setVatIsActive(1)
            ->setVatPercentage(20.0000)
            ->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(208.06, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(211.30, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(215.83, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-206
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listings_amazon_shipping_rate.php
     */
    public function testGlobalPriceAmazonShippingRate()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(40.94, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(42.98, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(45.84, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(47.94, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(49.98, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.84, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-207
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listings_amazon_shipping_rate.php
     */
    public function testGlobalPriceVatAmazonShippingRate()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setVatIsActive(1)
            ->setVatPercentage(20.0000);
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(49.13, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(51.58, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(55.01, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(56.13, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(58.58, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(60.01, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-208
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listings_amazon_shipping_rate.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testGlobalPriceCurrencyConversionAmazonShippingRate()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMarketplace(14)
            ->setDefaultCurrency('AUD')
            ->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(54.21, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(56.92, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(60.69, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(61.21, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(63.92, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(65.69, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-209
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listings_amazon_shipping_rate.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_currency_rates.php
     */
    public function testGlobalPriceVatCurrencyConversionAmazonShippingRate()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setMarketplace(14)
            ->setDefaultCurrency('AUD')
            ->setVatIsActive(1)
            ->setVatPercentage(20.0000)
            ->setCcIsActive(1)
            ->setCcRate('AUD');
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(65.05, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(68.30, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(72.83, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(72.05, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(75.30, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(77.83, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-210
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_price.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listings_amazon_shipping_rate.php
     */
    public function testCustomPriceAmazonShippingRate()
    {
        $account = $this->getAccountByName('mage-test');
        $accountListingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\Account\AccountListingRepository::class
        );
        $accountListing = $accountListingRepository->getByMerchantId($account->getMerchantId());
        $accountListing->setPriceField('amazon_price');
        $accountListingRepository->save($accountListing);

        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(130.94, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(132.98, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(135.84, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(137.94, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(139.98, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(140.84, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-1448
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listings_no_shipping_rate.php
     */
    public function testNoShippingRate()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByPercentRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(40.94, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(42.98, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(45.84, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    private function getSampleByPercentRule($accountId)
    {
        $rule = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\Rule::class
        );
        $rule->setName('test-rule-1')
            ->setMerchantId($accountId)
            ->setWebsiteIds(1)
            ->setIsActive(1)
            ->setConditionsSerialized(
                $this->getTestFileContents('pricing_rule_empty_condition.json')
            )
            ->setPriceMovement(2)
            ->setStopRulesProcessing(0)
            ->setSimpleAction('by_percent')
            ->setDiscountAmount(10.00);

        return $rule;
    }
}
