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
class PricingRulesIntelligentRulesTest extends PricingTestCase
{
    /**
     * CHAN-291
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_pricing_B002LAS6Z8.php
     */
    public function testLowestPriceUseAllConditions()
    {
        // Configure rules
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleRule($account->getMerchantId());
        $rule->setAuto(1)
            ->setAutoSource(1)
            ->setPriceMovement(0)
            ->setFloor('price')
            ->setFloorPriceMovement(0)
            ->setSortOrder(2)
            ->setAutoCondition(0)
            ->setCeiling('price')
            ->setCeilingPriceMovement(1)
            ->setCeilingSimpleAction('by_percent')
            ->setCeilingDiscountAmount('200');
        $pricingRuleRepository->save($rule);

        // Same condition exists
        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(59.80, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        // Same condition does not exist
        $this->updateCondition('B002LAS6Z8', 4);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(51.95, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    /**
     * CHAN-292
     *
     * @magentoDataFixture Magento_Amazon::Test/Integration/_files/indexer_amazon_account_pricing_B002LAS6Z8.php
     */
    public function testLowestPriceUseOnlyMatchingConditions()
    {
        // Configure rules
        $account = $this->getAccountByName('mage-test');
        $pricingRuleRepository = $this->objectManager->create(
            \Magento\Amazon\Model\Amazon\Pricing\PricingRuleRepository::class
        );
        $rule = $this->getSampleRule($account->getMerchantId());
        $rule->setAuto(1)
            ->setAutoSource(1)
            ->setPriceMovement(0)
            ->setFloor('price')
            ->setFloorPriceMovement(0)
            ->setSortOrder(2)
            ->setAutoCondition(1)
            ->setCeiling('price')
            ->setCeilingPriceMovement(1)
            ->setCeilingSimpleAction('by_percent')
            ->setCeilingDiscountAmount('200');
        $pricingRuleRepository->save($rule);

        // Same condition exists
        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(59.80, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));

        // Same condition does not exist
        $this->updateCondition('B002LAS6Z8', 4);

        $this->indexer->executeFull();

        $listings = $this->getListingsByAccount($account);

        $this->assertEquals(50.93, $this->getListingPriceBySku($listings, 'B002LAS6Z8'));
    }

    private function updateCondition($asin, $condition)
    {
        $listingRepository = $this->objectManager->get(
            \Magento\Amazon\Model\Amazon\ListingRepository::class
        );
        $listingCollectionFactory = $this->objectManager->create(
            \Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory::class
        );
        $listings = $listingCollectionFactory->create()
            ->addFieldToFilter('asin', $asin);
        foreach ($listings as $listing) {
            $listing->setCondition($condition);
            $listingRepository->save($listing);
        }
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
