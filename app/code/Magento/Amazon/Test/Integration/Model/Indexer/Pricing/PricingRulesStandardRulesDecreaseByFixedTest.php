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
class PricingRulesStandardRulesDecreaseByFixedTest extends PricingTestCase
{
    /**
     * CHAN-211
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testGlobalPrice()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(35.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(37.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(40.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-212
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(42.59, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(45.31, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(49.12, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-213
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(50.23, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(53.24, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(57.43, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-214
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(60.28, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(63.89, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(68.92, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-215
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(219.17, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(222.77, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(227.81, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-232
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listings_amazon_shipping_rate.php
     */
    public function testGlobalPriceAmazonShippingRate()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(35.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(37.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(40.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(42.49, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(44.76, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(45.93, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-233
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(42.59, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(45.31, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(49.12, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(49.59, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(52.31, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(54.12, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-234
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(50.23, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(53.24, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(57.43, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(57.23, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(60.24, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(62.43, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-235
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(60.28, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(63.89, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(68.92, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(67.28, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(70.89, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(73.92, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-236
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(135.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(137.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(140.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(142.49, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(144.76, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(145.93, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
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
        $rule = $this->getSampleByFixedRule($account->getMerchantId());
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(35.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(37.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(40.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    private function getSampleByFixedRule($accountId)
    {
        $rule = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\Rule::class
        );
        $rule->setName('test-rule-2')
            ->setMerchantId($accountId)
            ->setWebsiteIds(1)
            ->setIsActive(1)
            ->setConditionsSerialized(
                $this->getTestFileContents('pricing_rule_empty_condition.json')
            )
            ->setPriceMovement(2)
            ->setStopRulesProcessing(0)
            ->setSimpleAction('by_fixed')
            ->setDiscountAmount(10.00);

        return $rule;
    }
}
