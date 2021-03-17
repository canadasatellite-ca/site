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
class PricingRulesGeneralSettingsTest extends PricingTestCase
{
    /**
     * CHAN-175
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testStatusActive()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleRule($account->getMerchantId());
        $rule->setIsActive(1);
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(36.39, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(38.21, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(40.74, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-176
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testStatusInactive()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleRule($account->getMerchantId());
        $rule->setIsActive(0);
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(45.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(47.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-177
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testWebsiteAssociation()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleRule($account->getMerchantId());
        $rule->setWebsiteIds(1);
        $pricingRuleRepository->save($rule);

        $productRepository = $this->objectManager->get(
            \Magento\Catalog\Model\ProductRepository::class
        );
        $product = $productRepository->getById(2);
        $product->setWebsiteIds([0]);
        $productRepository->save($product);

        $product = $productRepository->getById(3);
        $product->setWebsiteIds([0]);
        $productRepository->save($product);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(36.39, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(47.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-178
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testFromToDate()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        // Inside date range
        $rule = $this->getSampleRule($account->getMerchantId());
        $rule->setFromDate(date('Y-m-d', strtotime('-2 day')));
        $rule->setToDate(date('Y-m-d', strtotime('+2 day')));
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(36.39, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(38.21, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(40.74, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        // Outside date range
        $rule->setFromDate(date('Y-m-d', strtotime('-4 day')));
        $rule->setToDate(date('Y-m-d', strtotime('-2 day')));
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(45.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(47.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-179
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testPriorityStandard()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule1 = $this->getSampleRule($account->getMerchantId());
        $rule1->setSimpleAction('by_percent')
            ->setDiscountAmount(10.00)
            ->setSortOrder(1);

        $pricingRuleRepository->save($rule1);

        $rule2 = $this->getSampleRule($account->getMerchantId());
        $rule2->setSimpleAction('by_fixed')
            ->setDiscountAmount(10.00)
            ->setSortOrder(2);
        $pricingRuleRepository->save($rule2);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(30.94, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(32.98, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(35.84, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-180
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_pricing_B002LAS6YY.php
     */
    public function testPriorityIntelligent()
    {
        $account = $this->getAccountByName('mage-test');

        // Configure rules
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule1 = $this->getSampleRule($account->getMerchantId());
        $rule1->setAuto(1)
            ->setAutoSource(1)
            ->setPriceMovement(0)
            ->setFloor('price')
            ->setFloorPriceMovement(0)
            ->setSortOrder(1);
        $pricingRuleRepository->save($rule1);

        $rule2 = $this->getSampleRule($account->getMerchantId());
        $rule2->setAuto(1)
            ->setAutoSource(0)
            ->setPriceMovement(0)
            ->setFloor('price')
            ->setFloorPriceMovement(0)
            ->setSortOrder(2);
        $pricingRuleRepository->save($rule2);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(45.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(50.90, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-181
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_pricing_B002LAS6YY.php
     */
    public function testPriorityMixed()
    {
        $account = $this->getAccountByName('mage-test');

        // Configure rules
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule1 = $this->getSampleRule($account->getMerchantId());
        $rule1->setPriceMovement(2)
            ->setSimpleAction('by_percent')
            ->setDiscountAmount(10.00)
            ->setSortOrder(1);
        $pricingRuleRepository->save($rule1);

        $rule2 = $this->getSampleRule($account->getMerchantId());
        $rule2->setAuto(1)
            ->setAutoSource(0)
            ->setPriceMovement(0)
            ->setFloor('price')
            ->setFloorPriceMovement(0)
            ->setSortOrder(2)
            ->setCeiling('price')
            ->setCeilingPriceMovement(1)
            ->setCeilingSimpleAction('by_percent')
            ->setCeilingDiscountAmount('200');
        $pricingRuleRepository->save($rule2);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(40.94, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(50.90, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(45.84, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-182
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_listing_rule.php
     */
    public function testDiscardSubsequentRules()
    {
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule1 = $this->getSampleRule($account->getMerchantId());
        $rule1->setSimpleAction('by_percent')
            ->setDiscountAmount(10.00)
            ->setSortOrder(2)
            ->setStopRulesProcessing(0);
        $pricingRuleRepository->save($rule1);

        $rule2 = $this->getSampleRule($account->getMerchantId());
        $rule2->setSimpleAction('by_fixed')
            ->setDiscountAmount(10.00)
            ->setSortOrder(1)
            ->setStopRulesProcessing(1);
        $pricingRuleRepository->save($rule2);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(35.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(37.76, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(40.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    private function getSampleRule($accountId)
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
            ->setDiscountAmount(20.00);

        return $rule;
    }
}
