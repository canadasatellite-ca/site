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
class PricingRulesStandardRulesIncreaseByPercentageTest extends PricingTestCase
{
    /**
     * CHAN-237
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

        $this->assertEquals(50.04, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(52.54, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(56.02, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-238
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

        $this->assertEquals(60.05, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(63.05, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(67.22, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-239
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

        $this->assertEquals(66.25, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(69.56, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(74.17, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-240
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

        $this->assertEquals(79.50, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(83.47, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(89.00, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-241
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

        $this->assertEquals(254.28, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(258.24, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(263.78, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-258
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

        $this->assertEquals(50.04, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(52.54, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(56.02, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(57.04, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(59.54, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(61.02, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-259
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

        $this->assertEquals(60.05, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(63.05, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(67.22, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(67.05, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(70.05, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(72.22, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-260
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

        $this->assertEquals(66.25, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(69.56, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(74.17, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(73.25, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(76.56, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(79.17, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-261
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

        $this->assertEquals(79.50, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(83.47, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(89.00, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(86.50, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(90.47, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(94.00, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-262
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

        $this->assertEquals(160.04, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(162.54, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(166.02, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        $this->assertEquals(167.04, $this->getLandedPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(169.54, $this->getLandedPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(171.02, $this->getLandedPriceBySku($listings, 'B002LAS6Z8'));
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

        $this->assertEquals(50.04, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(52.54, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(56.02, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    private function getSampleByPercentRule($accountId)
    {
        $rule = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\Rule::class
        );
        $rule->setName('test-rule-3')
            ->setMerchantId($accountId)
            ->setWebsiteIds(1)
            ->setIsActive(1)
            ->setConditionsSerialized(
                $this->getTestFileContents('pricing_rule_empty_condition.json')
            )
            ->setPriceMovement(1)
            ->setStopRulesProcessing(0)
            ->setSimpleAction('by_percent')
            ->setDiscountAmount(10.00);

        return $rule;
    }
}
