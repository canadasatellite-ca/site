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
class PricingRulesStandardRulesIncreaseByFixedTest extends PricingTestCase
{
    /**
     * CHAN-263
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

        $this->assertEquals(55.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(57.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(60.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-264
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

        $this->assertEquals(66.59, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(69.31, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(73.12, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-265
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

        $this->assertEquals(70.23, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(73.24, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(77.43, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-266
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

        $this->assertEquals(84.28, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(87.89, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(92.92, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-267
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

        $this->assertEquals(243.17, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(246.77, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(251.81, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-284
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

        $this->assertEquals(55.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(57.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(60.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(62.49, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(64.76, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(65.93, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-285
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

        $this->assertEquals(66.59, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(69.31, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(73.12, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(73.59, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(76.31, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(78.12, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-286
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

        $this->assertEquals(70.23, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(73.24, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(77.43, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(77.23, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(80.24, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(82.43, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-287
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

        $this->assertEquals(84.28, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(87.89, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(92.92, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(91.28, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(94.89, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(97.92, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-288
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

        $this->assertEquals(155.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(157.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(160.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(162.49, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(164.76, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(165.93, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
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

        $this->assertEquals(55.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(57.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(60.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
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
            ->setPriceMovement(1)
            ->setStopRulesProcessing(0)
            ->setSimpleAction('by_fixed')
            ->setDiscountAmount(10.00);

        return $rule;
    }
}
