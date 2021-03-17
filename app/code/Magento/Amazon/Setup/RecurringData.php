<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Setup;

use Magento\Store\Model\StoreManagerInterface;

class RecurringData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $this->addDefaultWebsite($setup);
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    private function addDefaultWebsite(\Magento\Framework\Setup\ModuleDataSetupInterface $setup): void
    {
        $connection = $setup->getConnection();
        $accountsTable = $setup->getTable('channel_amazon_account');
        $listingRuleTable = $setup->getTable('channel_amazon_listing_rule');
        $accountsWithoutWebsitesSelect = $connection->select()
            ->from(['account' => $accountsTable], ['merchant_id'])
            ->joinLeft(['rule' => $listingRuleTable], 'account.merchant_id = rule.merchant_id', [])
            ->where('rule.website_id IS NULL');
        $merchantIds = $connection->fetchCol($accountsWithoutWebsitesSelect);
        if (!$merchantIds) {
            return;
        }

        $websites = $this->storeManager->getWebsites();
        $defaultWebsite = null;
        foreach ($websites as $website) {
            $isDefault = $website->getData('is_default');
            if (null === $defaultWebsite || $isDefault) {
                $defaultWebsite = $website;
            }
        }
        if (!$defaultWebsite) {
            return;
        }

        $data = [];
        foreach ($merchantIds as $merchantId) {
            $data[] = [$merchantId, '{}', $defaultWebsite->getId()];
        }
        $connection->insertArray(
            $listingRuleTable,
            ['merchant_id', 'conditions_serialized', 'website_id'],
            $data
        );
    }
}
