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
class PricingRulesConditionsTest extends PricingTestCase
{
    /**
     * CHAN-183
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_asin.php
     */
    public function testCondition()
    {
        $pricingRuleCondition = trim($this->getTestFileContents('pricing_rule_amazon_asin_B002LAS6YY.json'));
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleConditionRule($account->getMerchantId());
        $rule->setConditionsSerialized($pricingRuleCondition);
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(45.49, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(42.98, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_product_attribute_asin.php
     */
    public function testConditionCombination()
    {
        $pricingRuleCondition = $this->getTestFileContents('pricing_rule_amazon_asin_B002LAS6YE_or_B002LAS6YY.json');
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleConditionRule($account->getMerchantId());
        $rule->setConditionsSerialized($pricingRuleCondition);
        $pricingRuleRepository->save($rule);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(40.94, $this->getListingPriceBySku($listings, 'B002LAS6YE'));
        $this->assertEquals(42.98, $this->getListingPriceBySku($listings, 'B002LAS6YY'));
        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    private function getSampleConditionRule($accountId)
    {
        $rule = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\Rule::class
        );
        $rule->setName('test-rule-1')
            ->setMerchantId($accountId)
            ->setWebsiteIds(1)
            ->setIsActive(1)
            ->setPriceMovement(2)
            ->setStopRulesProcessing(0)
            ->setSimpleAction('by_percent')
            ->setDiscountAmount(10.00);

        return $rule;
    }
}
